<?php

namespace App\Http\Controllers\Api\Biller;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreBillerProduct;
use App\Models\TransactionDefinitionV2;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class CoreBillerProductController extends Controller
{
    use ResponseHandler;

    public function getPaginate()
    {
        return [
            'CSC_BP_ID AS ID',
            'CSC_BP_PRODUCT AS PRODUCT',
            'BILLER.CSC_BILLER_GROUP_PRODUCT AS GROUP_PRODUCT'
        ];
    }

    public function cekBiller($id)
    {
        return CoreBiller::searchData($id)->first('CSC_BILLER_ID AS BILLER_ID');
    }

    public function index(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(['biller_id' => ['required', 'string', 'max:5']]);

            // Inisialisasi Varriable
            $items = ($request->items == null) ? 10 : $request->items;
            $biller_id = $request->biller_id;

            // Cek Data Biller
            $cekBiller = self::cekBiller($biller_id);
            if (null == $cekBiller) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

            // Logic Get Data Biller Product
            $data = CoreBillerProduct::join(
                'CSCCORE_TRANSACTION_DEFINITION AS PRODUCT',
                'PRODUCT.CSC_TD_NAME',
                '=',
                'CSC_BP_PRODUCT'
            )
            ->join(
                'CSCCORE_BILLER AS BILLER',
                'BILLER.CSC_BILLER_ID',
                '=',
                'CSC_BP_BILLER'
            )
            ->where('CSC_BP_BILLER', $biller_id)
            ->paginate(
                $items = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (count($data) != null) {
                return $this->generalDataResponse(
                    200,
                    'Get List Data Product-Biller Success',
                    $data
                );
            }

            // Response Not Found
            if (count($data) == null) {
                return $this->generalResponse(
                    404,
                    'Data Product-Biller Not Found',
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Data Product-Biller Failed',
                );
            }
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi Request Input
            $request->validate(
                [
                    'biller_id' => ['required', 'string', 'max:5'],
                    'product' => ['required'],
                    'product.*' => ['required', 'string', 'max:100'],
                ]
            );

            if (!is_array($request->product)) {
                return response()->json(
                    new ResponseResource(400, 'Invalid Data Validation'),
                    400
                );
            }

            $cekBiller = self::cekBiller($request->biller_id);
            if (null == $cekBiller) {
                return response(
                    new ResponseResource(404, 'Data Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $biller = $request->biller_id;
            $products = $request->product;
            $countProduct = count($products);
            $warningExists = [];
            $warningNotRegistered = [];

            for ($i=0; $i < $countProduct; $i++) {
                // Validasi Data Produk ke tabel Transaction Definition
                $cekProduct[$i] = TransactionDefinitionV2::searchData($products[$i])->first('CSC_TD_NAME');
                $cekExists[$i] = CoreBillerProduct::cekProduct($products[$i])->first('CSC_BP_PRODUCT');

                if ($cekProduct[$i] == null && $cekExists[$i] != null) {
                    //  Cek Product:False, Cek Exists: False
                    $warningNotRegistered[] = $products[$i];
                    $warningExists[] = $products[$i];
                    unset($products[$i]);
                } elseif ($cekProduct[$i] == null && $cekExists[$i] == null) {
                    //  Cek Product:False, Cek Exists: True
                    $warningNotRegistered[] = $products[$i];
                    unset($products[$i]);
                } elseif ($cekProduct[$i] != null && $cekExists[$i] != null) {
                    //  Cek Product:True, Cek Exists: False
                    $warningExists[] = $products[$i];
                    unset($products[$i]);
                } else {
                    //  Cek Product:True, Cek Exists: True
                    $keterangan = null;
                    while ($keterangan == false) {
                        $id = Uuid::uuid4();
                        $cekId = CoreBillerProduct::searchData($id)->first();

                        if (null == $cekId) {
                            $keterangan = true;
                        } else {
                            $keterangan = false;
                        }
                    }

                    CoreBillerProduct::create(
                        [
                            'CSC_BP_ID' => $id,
                            'CSC_BP_PRODUCT' => $products[$i],
                            'CSC_BP_BILLER' => $biller,
                        ]
                    );
                }
            }

            if ($warningExists == null && $warningNotRegistered == null) {
                return response()->json(
                    new ResponseResource(200, 'Insert Data Product-Biller Success'),
                    200
                );
            } elseif (null != $warningExists && null != $warningNotRegistered) {
                $response['product_exists'] = $warningExists;
                $response['product_not_registered'] = $warningNotRegistered;

                return $this->generalDataResponse(
                    202,
                    'Insert Data Product-Biller Success But Some Product Cannot Processed',
                    $response
                );
            } elseif (null != $warningExists) {
                $response['product_exists'] = $warningExists;

                return $this->generalDataResponse(
                    202,
                    'Insert Data Product-Biller Success But Some Product Exists',
                    $response
                );
            } elseif (null != $warningNotRegistered) {
                $response['product_not_registered'] = $warningNotRegistered;

                return $this->generalDataResponse(
                    202,
                    'Insert Data Product-Biller Success But Some Product Not Registered',
                    $response
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Insert Data Product-Biller Failed'
                    ),
                    500
                );
            }
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()), 400);
        }
    }

    public function destroy($id)
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

                $data = CoreBillerProduct::searchData($id)->first();
                if (null == $data) {
                    return response(
                        new ResponseResource(404, 'Data Product-Biller Not Found'),
                        404,
                        ['Accept' => 'Application/json']
                    );
                }

                $destroy = $data->delete();

                if ($destroy) {
                    return response()->json(
                        new ResponseResource(200, 'Delete Data Product-Biller Success'),
                        200
                    );
                } else {
                    return response()->json(
                        new DataResponseResource(500, 'Update Data Product-Biller Failed', $data),
                        500
                    );
                }
            } catch (ValidationException $th) {
                return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
            }
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }

    public function listAddProduct(Request $request)
    {
        try {
            // Inisialisasi Variable
            $items = ($request->items != null) ? $request->items : 10;

            // Logic Get Data List Add Product
            $data = TransactionDefinitionV2::whereNotExists(function ($query) {
                $query->select('BP.CSC_BP_PRODUCT')
                ->from('CSCCORE_BILLER_PRODUCT AS BP')
                ->whereColumn('BP.CSC_BP_PRODUCT', 'CSC_TD_NAME');
            })
            ->paginate(
                $items = $items,
                $column = [
                    'CSC_TD_NAME AS PRODUCT'
                ]
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (null != count($data)) {
                return $this->generalDataResponse(
                    200,
                    'Get List Add Product-Biller Success',
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Add Product-Biller Not Found'
                );
            }
        } catch (\Throwable $th) {
            return $this->generalResponse(
                500,
                'Get List Data Add Product-Biller Failed'
            );
        }
    }
}
