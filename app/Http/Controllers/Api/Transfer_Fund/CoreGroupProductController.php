<?php

namespace App\Http\Controllers\Api\Transfer_Fund;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreBillerProduct;
use App\Models\CoreGroupTransferFunds;
use App\Models\CoreProductFunds;
use App\Models\TransactionDefinitionV2;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

use function PHPSTORM_META\map;
use function PHPUnit\Framework\returnSelf;

class CoreGroupProductController extends Controller
{
    public function listProduct(Request $request)
    {
        try {
            $request->validate(
                [
                    'group_transfer' => ['required', 'string', 'max:50'],
                    'items' => ['numeric'],
                ]
            );

            $groupFund = $request->group_transfer;
            $items = ($request->items != null) ? $request->items : 10;

            $cekGroupFund = CoreGroupTransferFunds::searchData($groupFund)->first('CSC_GTF_ID');
            if (null == $cekGroupFund) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Group Transfer Funds Not Found'
                    ),
                    404
                );
            }

            $data = CoreProductFunds::where('CSC_PF_GROUP_TRANSFER', $groupFund)
            ->paginate(
                $items = $items,
                $column = [
                    'CSC_PF_ID AS ID',
                    'CSC_PF_PRODUCT AS PRODUCT'
                ]
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null != count($data)) {
                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get List Product-Group Transfer Funds Success',
                        $data
                    ),
                    200
                );
            } elseif (null == count($data)) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Product-Group Transfer Funds Not Found'
                    ),
                    404
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Get List Product-Group Transfer Funds Failed'
                    ),
                    500
                );
            }
        } catch (ValidationException $th) {
            return response()->json(
                new DataResponseResource(
                    400,
                    'Invalid Data Validation',
                    $th->validator->errors()
                ),
                400
            );
        }
    }

    public function listAddProduct(Request $request, $config)
    {
        // Validasi Config
        if ('simple' != $config && 'detail' != $config) :
            return $this->invalidValidation();
        endif;

        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'biller_id' => ['required', 'string', 'max:5'],
                    'items' => ['numeric', 'max:100'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $biller = (false == $request->biller_id) ? null : $request->biller_id;
        $items = (null == $request->items) ? 10 : $request->items;

        // Cek Biller
        if (null != $biller) :
            $cekBiller = CoreBiller::searchData($biller)->first();
            if (null == $cekBiller) :
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            endif;
        endif;

        // Logic Detail Config
        if ('detail' == $config) :
            $data = CoreBillerProduct::join(
                'CSCCORE_TRANSACTION_DEFINITION AS TD',
                'CSC_BP_PRODUCT',
                '=',
                'TD.CSC_TD_NAME'
            )
            ->whereNotExists(
                function ($query) {
                    $query->from('CSCCORE_PRODUCT_FUNDS AS PF')
                    ->select('PF.CSC_PF_PRODUCT')
                    ->whereColumn('PF.CSC_PF_PRODUCT', 'TD.CSC_TD_NAME');
                }
            )
            ->where(
                function ($query) use ($biller) {
                    if (null != $biller) :
                        $query->where('CSC_BP_BILLER', $biller);
                    endif;
                }
            )
            ->paginate(
                $items,
                $column = [
                    'TD.CSC_TD_NAME AS PRODUCT'
                ]
            );

            // Hitung Data
            $countData = count($data);

            // Add Index Number
            if (null != $countData) :
                $data = $this->addIndexNumber($data);
            endif;
        endif;

        // Logic Simple Config
        if ('simple' == $config) :
            $data = CoreBillerProduct::join(
                'CSCCORE_TRANSACTION_DEFINITION AS TD',
                'CSC_BP_PRODUCT',
                '=',
                'TD.CSC_TD_NAME'
            )
            ->whereNotExists(
                function ($query) {
                    $query->from('CSCCORE_PRODUCT_FUNDS AS PF')
                    ->select('PF.CSC_PF_PRODUCT')
                    ->whereColumn('PF.CSC_PF_PRODUCT', 'TD.CSC_TD_NAME');
                }
            )
            ->where(
                function ($query) use ($biller) {
                    if (null != $biller) :
                        $query->where('CSC_BP_BILLER', $biller);
                    endif;
                }
            )
            ->get('TD.CSC_TD_NAME AS PRODUCT');

            // Hitung Data
            $countData = count($data);
        endif;

        // return $data;

        // Response Not Found
        if (null == $countData) :
            return $this->responseNotFound('Data Add Product-Group Transfer Funds Not Found');
        endif;

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Get List Add Product-Group Transfer Funds Failed');
        endif;

        // Response Success
        if ($data) :
            return $this->generalDataResponse(200, 'Get List Add Product-Group Transfer Funds Success', $data);
        endif;
    }

    public function addProduct(Request $request)
    {
        try {
            $request->validate(
                [
                    'group_transfer' => ['required', 'string', 'max:50'],
                    'product' => ['required'],
                    'product.*' => ['string', 'max:100'],
                ]
            );

            $groupTransfer = $request->group_transfer;
            $cekGroupTransfer = CoreGroupTransferFunds::searchData($groupTransfer)->first();
            if (null == $cekGroupTransfer) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Group Transfer Funds Not Found'
                    ),
                    404
                );
            }

            $product = $request->product;
            $countProduct = count($product);
            $totalProductGtf = $cekGroupTransfer->CSC_GTF_PRODUCT_COUNT;
            $warningExist = [];
            $warningNotRegistered = [];

            // return $cekGroupTransfer->CSC_GTF_PRODUCT_COUNT = $totalProductGtf + 1;

            // Cek Product
            for ($i=0; $i < $countProduct; $i++) {
                $cekProduct[$i] = TransactionDefinitionV2::searchData($product[$i])->first('CSC_TD_NAME');
                $cekExist[$i] = CoreProductFunds::searchProduct($product[$i])->first('CSC_PF_ID');

                // NULL::$cekProduct == false
                // NULL::$cekAxists == true

                if (null == $cekProduct[$i] && null == $cekExist[$i]) {
                    $warningNotRegistered[] = $product[$i];
                    unset($product[$i]);
                } elseif (null != $cekProduct[$i] && null != $cekExist[$i]) {
                    $warningExist[] = $product[$i];
                    unset($product[$i]);
                } elseif (null == $cekProduct && null != $cekExist[$i]) {
                    $warningExist[] = $product[$i];
                    $warningNotRegistered[] = $product[$i];
                    unset($product[$i]);
                } else {
                    $keterangan = null;
                    while ($keterangan == false) {
                        $id = Uuid::uuid4();
                        $cekId = CoreProductFunds::searchById($id)->first();

                        if (null == $cekId) {
                            $keterangan = true;
                        } else {
                            $keterangan = false;
                        }
                    }
                    $add = CoreProductFunds::create(
                        [
                            'CSC_PF_ID' => $id,
                            'CSC_PF_PRODUCT' => $product[$i],
                            'CSC_PF_GROUP_TRANSFER' => $groupTransfer,
                        ]
                    );

                    $cekGroupTransfer->CSC_GTF_PRODUCT_COUNT = $totalProductGtf + 1;
                    $cekGroupTransfer->save();

                    if (!$add) {
                        return response()->json(
                            new ResponseResource(
                                500,
                                'Insert Data Product-Group Transfer Funds Failed'
                            ),
                            500
                        );
                    }
                }
            }

            if (null == $warningExist && null == $warningNotRegistered) {
                return $this->generalResponse(200, 'Insert Data Product-Group Transfer Funds Success');
            } elseif (null != $warningExist && null != $warningNotRegistered) {
                $message = 'Insert Data Product-Group Transfer Funds Success but Some Product Cannot Processed';
                // ($warningExist == null) ?: $response['product_exists'] = $warningExist;
                // ($warningNotRegistered == null) ?: $response['product_not_registered'] = $warningNotRegistered;

                $response['product_exists'] = $warningExist;
                $response['product_not_registered'] = $warningNotRegistered;

                return $this->generalDataResponse(
                    202,
                    $message,
                    $response
                );
            } elseif (null != $warningExist) {
                $message = 'Insert Data Product-Group Transfer Funds Success But Some Product Exists';
                $response['product_exists'] = $warningExist;

                return $this->generalDataResponse(
                    202,
                    $message,
                    $response
                );
            } elseif (null != $warningNotRegistered) {
                $message = 'Insert Data Product-Group Transfer Funds Success But Some Product Not Registered';
                $response['product_not_registered'] =  $warningNotRegistered;

                return $this->generalDataResponse(
                    202,
                    $message,
                    $response
                );
            } else {
                return $this->failedResponse('Insert Data Product-Group Transfer Funds Failed');
            }
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }
    }

    public function deleteProduct($id)
    {
        if (null != $id) {
            try {
                if (Str::length($id) > 36) {
                    $id = ['The id must not be greater than 36 characters.'];

                    return response(
                        new DataResponseResource(400, 'Invalid Data Validation', $id),
                        400,
                        ['Accept' => 'application/json']
                    );
                }

                // Cek GTF
                $data = CoreProductFunds::searchById($id)
                ->join(
                    'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
                    'CSC_PF_GROUP_TRANSFER',
                    '=',
                    'GTF.CSC_GTF_ID'
                )
                ->whereNull('GTF.CSC_GTF_DELETED_DT')
                ->first();
                if (null == $data) {
                    return $this->generalResponse(
                        404,
                        'Data Product-Group Transfer Funds Not Found'
                    );
                }

                $gtf = CoreGroupTransferFunds::searchData($data->CSC_GTF_ID)->first();

                // Logic Delete Data
                $data->delete();
                $gtf->CSC_GTF_PRODUCT_COUNT--;
                $gtf->save();

                // Response Sukses
                if ($data) {
                    return response(
                        new ResponseResource(200, 'Delete Data Product-Group Transfer Funds Success'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(500, 'Delete Data Product-Group Transfer Funds Failed'),
                    500,
                    ['Accept' => 'Application/json']
                );
            } catch (ValidationException $th) {
                return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
            }
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }
}
