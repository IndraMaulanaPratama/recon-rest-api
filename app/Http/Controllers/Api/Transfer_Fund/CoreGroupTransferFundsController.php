<?php

namespace App\Http\Controllers\Api\Transfer_Fund;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreAccount;
use App\Models\CoreBiller;
use App\Models\CoreGroupTransferFunds;
use App\Models\CoreProductFunds;
use App\Models\TransactionDefinitionV2;
use App\Traits\GroupTransferTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CoreGroupTransferFundsController extends Controller
{
    use ResponseHandler;
    use GroupTransferTraits;

    public function getClientId($bearer)
    {
        $bearer_value = substr($bearer, 7);
        $clientId = DB::connection('recon_auth')
        ->table('CSCMOD_CLIENT')
        ->where('csm_c_bearer', $bearer_value)
        ->pluck('csm_c_id');

        return $clientId[0];
    }

    public function getField()
    {
        return [
            'CSC_GTF_ID AS ID',
            'CSC_GTF_NAME AS NAME',
            'CSC_GTF_SOURCE AS ACCOUNT_SRC',
            'CSC_GTF_DESTINATION AS ACCOUNT_DEST',
            'CSC_GTF_TRANSFER_TYPE AS TYPE',
            'CSC_GTF_TRANSFER_DESC AS TRANSFER_DESC',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_GTF_ID AS ID',
            'CSC_GTF_NAME AS NAME',
            'CSC_GTF_SOURCE AS ACCOUNT_SRC',
            'CSC_GTF_DESTINATION AS ACCOUNT_DEST',
            'CSC_GTF_TRANSFER_TYPE AS TYPE',
            'CSC_GTF_TRANSFER_DESC AS TRANSFER_DESC',
            'CSC_GTF_PRODUCT_COUNT AS PRODUCT_COUNT',
            'CSC_GTF_CREATED_DT AS CREATED',
            'CSC_GTF_CREATED_BY AS CREATED_BY',
            'CSC_GTF_MODIFIED_DT AS MODIFIED',
            'CSC_GTF_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function cekModul($modul, $biller)
    {
        $cekModul = DB::connection('server_report')
        ->table('CSCCORE_BILLER AS BILLER')
        ->join(
            'CSCCORE_GROUP_OF_PRODUCT as GROUP_OF_PRODUCT',
            'BILLER.CSC_BILLER_GROUP_PRODUCT',
            '=',
            'GROUP_OF_PRODUCT.CSC_GOP_PRODUCT_GROUP'
        )
        ->where(
            function ($query) use ($modul, $biller) {
                if (null != $modul && null == $biller) :
                    $query->where(
                        'GROUP_OF_PRODUCT.CSC_GOP_PRODUCT_PARENT_PRODUCT',
                        $modul
                    );
                endif;

                if (null == $modul && null != $biller) :
                    $query->where(
                        'BILLER.CSC_BILLER_GROUP_PRODUCT',
                        $biller
                    );
                endif;

                if (null != $modul && null != $biller) :
                    $query->where(
                        [
                            'GROUP_OF_PRODUCT.CSC_GOP_PRODUCT_PARENT_PRODUCT' => $modul,
                            'BILLER.CSC_BILLER_GROUP_PRODUCT' => $biller,
                        ]
                    );
                endif;
            }
        )
        ->first('BILLER.CSC_BILLER_GROUP_PRODUCT AS BILLER_GROUP_PRODUCT');

        return $cekModul;
    }

    public function index(Request $request, $config)
    {
        // Inisialisasi Data Mandatori
        try {
            $request->validate(
                [
                    'modul' => ['required', 'string', 'max:50'],
                    'biller' => ['required', 'string', 'max:50'],
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
        $items = (null != $request->items) ? $request->items : 10;
        $modul = (false == $request->modul) ? null : $request->modul;
        $biller = (false == $request->biller) ? null : $request->biller;

        // Cek Modul
        if (null != $modul) :
            $cekModul = self::cekModul($modul, $biller);
            if ($cekModul == null) :
                return $this->generalResponse(
                    404,
                    'Filter Biller Not Found'
                );
            endif;
        endif;

        // Cek Biller
        if (null != $biller) :
            $cekBiller = CoreBiller::groupBiller($biller)->first();
            if (null == $cekBiller) :
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            endif;
        endif;

        // Logic Simple Config
        if ('simple' == $config) {
            // Logic Get Data
            $data = DB::connection('server_report')
            ->table('CSCCORE_GROUP_TRANSFER_FUNDS AS TRANSFER_FUNDS')
            ->join(
                'CSCCORE_PRODUCT_FUNDS AS PRODUCT_FUNDS',
                'TRANSFER_FUNDS.CSC_GTF_ID',
                '=',
                'PRODUCT_FUNDS.CSC_PF_GROUP_TRANSFER'
            )
            ->join(
                'CSCCORE_TRANSACTION_DEFINITION AS TRANSACTION_DEFINITION',
                'PRODUCT_FUNDS.CSC_PF_PRODUCT',
                '=',
                'TRANSACTION_DEFINITION.CSC_TD_NAME'
            )
            ->join(
                'CSCCORE_BILLER_PRODUCT AS BILLER_PRODUCT',
                'TRANSACTION_DEFINITION.CSC_TD_NAME',
                '=',
                'BILLER_PRODUCT.CSC_BP_PRODUCT'
            )
            ->join(
                'CSCCORE_BILLER AS BILLER',
                'BILLER_PRODUCT.CSC_BP_BILLER',
                '=',
                'BILLER.CSC_BILLER_ID'
            )

            ->where(function ($query) use ($biller, $modul) {
                if (null != $modul) :
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
                endif;

                if (null != $biller) {
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', 'LIKE', '%'. $biller .'%');
                }
            })
            ->whereNull('CSC_GTF_DELETED_DT')
            ->distinct()
            ->get(self::getField());

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Group Transfer Funds Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Group Transfer Funds Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Data Product-Group Transfer Funds Failed”'
                );
            }

        // Logic Detail Config
        } elseif ('detail' == $config) {
            // Logic Get Data
            $data = CoreGroupTransferFunds::join(
                'CSCCORE_PRODUCT_FUNDS AS PRODUCT_FUNDS',
                'CSC_GTF_ID',
                '=',
                'PRODUCT_FUNDS.CSC_PF_GROUP_TRANSFER'
            )
            ->join(
                'CSCCORE_TRANSACTION_DEFINITION AS TRANSACTION_DEFINITION',
                'PRODUCT_FUNDS.CSC_PF_PRODUCT',
                '=',
                'TRANSACTION_DEFINITION.CSC_TD_NAME'
            )
            ->join(
                'CSCCORE_BILLER_PRODUCT AS BILLER_PRODUCT',
                'TRANSACTION_DEFINITION.CSC_TD_NAME',
                '=',
                'BILLER_PRODUCT.CSC_BP_PRODUCT'
            )
            ->join(
                'CSCCORE_BILLER AS BILLER',
                'BILLER_PRODUCT.CSC_BP_BILLER',
                '=',
                'BILLER.CSC_BILLER_ID'
            )

            ->where(function ($query) use ($biller, $modul) {
                if (null != $modul) :
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
                endif;

                if (null != $biller) {
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', 'LIKE', '%'. $biller .'%');
                }
            })
            ->groupBy('CSC_GTF_ID')
            ->whereNull('CSC_GTF_DELETED_DT')
            ->paginate(
                $perpage = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Group Transfer Funds Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Group Transfer Funds Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Data Product-Group Transfer Funds Failed”'
                );
            }

        // Invalid Config
        } else {
            return $this->generalResponse(
                404,
                'Data Group Transfer Funds Not Found'
            );
        }
    }

    public function store(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate([
                'created_by' => ['required', 'string', 'max:50'],
                'id' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:100'],
                'account_src' => ['required', 'numeric', 'digits_between:1,20'],
                'account_dest' => ['required', 'numeric', 'digits_between:1,20'],
                'type' => ['required', 'numeric', 'digits:1'],
                'transfer_desc' => ['required', 'string', 'max:20'],
                'product' => ['required', 'array', 'min:1'],
                'product.*' => ['string', 'max:100'],
            ]);
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

        $cekAccountSrc = CoreAccount::searchData($request->account_src)->first('CSC_ACCOUNT_NUMBER AS ACCOUNT');
        $cekAccountDest = CoreAccount::searchData($request->account_dest)->first('CSC_ACCOUNT_NUMBER AS ACCOUNT');
        $cekExist = CoreGroupTransferFunds::searchData($request->id)->first('CSC_GTF_ID');
        $statusDeleted = CoreGroupTransferFunds::searchTrashData($request->id)->first('CSC_GTF_ID');

        if (null != $cekExist) {
            return response(
                new ResponseResource(409, 'Data Group Transfer Funds Exists'),
                409,
                ['Accept' => 'Application/json']
            );
        }

        if (null != $statusDeleted) {
            return response(
                new ResponseResource(422, 'Unprocessable Entity'),
                422,
                ['Accept' => 'Application/json']
            );
        }

        if (null == $cekAccountSrc || null == $cekAccountDest) {
            return response(
                new ResponseResource(404, 'Data Account Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }

        // Inisialisasi Variable
        $product = $request->product;
        $transferDesc = $request->transfer_desc;
        $countApi = count($product);
        $warninRegistered = [];
        $warninExist = [];

        // Logic Add Product Funds
        for ($i = 0; $i < $countApi; ++$i) {
            $cekData[$i] = TransactionDefinitionV2::searchData($product[$i])->first(['CSC_TD_NAME AS product']);
            $cekExist[$i] = CoreProductFunds::searchProduct($product[$i])->first([
                'CSC_PF_GROUP_TRANSFER AS ACCOUNT', 'CSC_PF_PRODUCT AS PRODUCT',
            ]); // null is true

            if (null == $cekData[$i] && null == $cekExist[$i]) {
                $warninRegistered[] = $product[$i];
                unset($product[$i]);
            } elseif (null != $cekData[$i] && null != $cekExist[$i]) {
                $warninExist[] = $cekExist[$i]->PRODUCT;
                unset($product[$i]);
            } elseif (null == $cekData[$i] && null != $cekExist[$i]) {
                $warninRegistered[] = $product[$i];
                $warninExist[] = $cekExist[$i]->PRODUCT;
                unset($product[$i]);
            } else {
                CoreProductFunds::create([
                    'CSC_PF_ID' => Uuid::uuid4(),
                    'CSC_PF_PRODUCT' => $product[$i],
                    'CSC_PF_GROUP_TRANSFER' => $request->id,
                ]);
            }
        }

        // END OF ZONE OF CSCCORE_PRODUCT_FUNDS //

        // ZONE OF CSCCORE_GROUP_TRANSFER FUNDS //

        if (null != count($product)) {
            $clientId = $request->created_by;
            $countProduct = CoreProductFunds::searchByAccount($request->id)->count();
            $store = CoreGroupTransferFunds::create(
                [
                    'CSC_GTF_ID' => $request->id,
                    'CSC_GTF_SOURCE' => $request->account_src,
                    'CSC_GTF_DESTINATION' => $request->account_dest,
                    'CSC_GTF_NAME' => $request->name,
                    'CSC_GTF_TRANSFER_TYPE' => $request->type,
                    'CSC_GTF_TRANSFER_DESC' => $transferDesc,
                    'CSC_GTF_PRODUCT_COUNT' => $countProduct,
                    'CSC_GTF_CREATED_BY' => $clientId,
                    'CSC_GTF_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                ]
            );

            if (!$store) {
                return response(
                    new DataResponseResource(500, 'Insert Data Group Transfer Funds Failed', $store),
                    500,
                    ['Accept' => 'Application/json']
                );
            }
        }

        // END OF ZONE OF CSCCORE_GROUP_TRANSFER FUNDS //

        if (null == $warninRegistered && null == $warninExist) {
            return response(
                new ResponseResource(200, 'Insert Data Group Transfer Funds Success'),
                200,
                ['Accept' => 'Application/json']
            );
        } elseif (null != $warninExist && null != $warninRegistered) {
            $response = [
                'product_exists' => $warninExist,
                'product_not_registered' => $warninRegistered,
            ];

            return $this->generalDataResponse(
                202,
                'Insert Data Group Transfer Funds Success but Some Product Cannot Processed',
                $response
            );
        } elseif (null != $warninExist) {
            $response = [
                'product_exists' => $warninExist,
            ];

            return $this->generalDataResponse(
                202,
                'Insert Data Group Transfer Funds Success but Some Product Exists',
                $response
            );
        } elseif (null != $warninRegistered) {
            $response = [
                'product_not_registered' => $warninRegistered,
            ];

            return $this->generalDataResponse(
                202,
                'Insert Data Group Transfer Funds Success but Some Product Not Registered',
                $response
            );
        } else {
            return response()->json(
                new ResponseResource(
                    500,
                    'Insert Data Group Transfer Funds Failed'
                ),
                500
            );
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:50']]);

            $data = CoreGroupTransferFunds::searchData($request->id)->first(self::getPaginate());

            if (null != $data) {
                return response(
                    new DataResponseResource(200, 'Get Data Group Transfer Funds Success', $data),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Group Transfer Funds Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
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

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(
                [
                    'modified_by' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:100'],
                    'account_src' => ['required', 'numeric', 'digits_between:1,20'],
                    'account_dest' => ['required', 'numeric', 'digits_between:1,20'],
                    'type' => ['required', 'integer', 'digits:1'],
                    'transfer_desc' => ['required', 'string', 'max:20'],
                    ]
            );

            // Inisialisasi Variabel
            $account_src = $request->account_src;
            $account_dest = $request->account_dest;
            $name = $request->name;
            $type = $request->type;
            $transferDesc = $request->transfer_desc;
            $clientId = $request->modified_by;

            // Cek Account
            $cekAccountSrc = CoreAccount::searchData($account_src)->first('CSC_ACCOUNT_NUMBER AS ACCOUNT');
            $cekAccountDest = CoreAccount::searchData($account_dest)->first('CSC_ACCOUNT_NUMBER AS ACCOUNT');
            if (null == $cekAccountSrc || null == $cekAccountDest) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Cek Status Deleted
            $statusDeleted = CoreGroupTransferFunds::searchTrashData($id)->first('CSC_GTF_ID');
            if (null != $statusDeleted) {
                return $this->generalResponse(
                    404,
                    'Data Group Transfer Funds Not Found'
                );
            }

            // Cek Data
            $data = CoreGroupTransferFunds::searchData($id)->first();
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Data Group Transfer Funds Not Found'
                );
            }

            // Logic Update Data
            $countProduct = CoreProductFunds::searchByAccount($id)->count();
            $data->CSC_GTF_PRODUCT_COUNT = $countProduct;
            $data->CSC_GTF_NAME = $name;
            $data->CSC_GTF_SOURCE = $account_src;
            $data->CSC_GTF_TRANSFER_TYPE = $type;
            $data->CSC_GTF_TRANSFER_DESC = $transferDesc;
            $data->CSC_GTF_DESTINATION = $account_dest;
            $data->CSC_GTF_MODIFIED_DT = Carbon::now('Asia/Jakarta');
            $data->CSC_GTF_MODIFIED_BY = $clientId;
            $data->save();

            // Response Sukses
            if ($data) {
                return $this->generalResponse(
                    200,
                    'Update Data Group Transfer Funds Success'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Update Data Group Transfer Funds Failed'
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

    public function destroy(Request $request, $id)
    {
        if (null != $id) {
            try {
                if (Str::length($id) > 50) {
                    $id = ['The id must not be greater than 50 characters.'];

                    return response(
                        new DataResponseResource(400, 'Invalid Data Validation', $id),
                        400,
                        ['Accept' => 'application/json']
                    );
                }

                $data = CoreGroupTransferFunds::searchData($id)->first();

                if (null != $data) {
                    $request->validate(
                        ['deleted_by' => ['required', 'string', 'max:50']]
                    );
                    $clientId = $request->deleted_by;
                    $data->CSC_GTF_DELETED_BY = $clientId;
                    $data->CSC_GTF_DELETED_DT = Carbon::now('Asia/Jakarta');
                    $data->save();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Group Transfer Funds Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Update Data Group Transfer Funds Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Group Transfer Funds Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
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
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'), 400);
        }
    }

    public function filter(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'modul' => ['required', 'string', 'max:100'],
                'biller' => ['required', 'string', 'max:50'],
                'items' => ['numeric', 'digits_between:1,3'],
                'account_src' => ['numeric', 'digits_between:1,20'],
                'account_dest' => ['numeric', 'digits_between:1,20'],
                'type' => ['numeric', 'digits:1'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;
        $modul = (false == $request->modul) ? null : $request->modul;
        $biller = (false == $request->biller) ? null : $request->biller;
        $accountSrc = $request->account_src;
        $accountDst = $request->account_dest;
        $type = $request->type;

        // Logic Get Data
        $data = CoreGroupTransferFunds::join(
            'CSCCORE_PRODUCT_FUNDS AS PRODUCT_FUNDS',
            'CSC_GTF_ID',
            '=',
            'PRODUCT_FUNDS.CSC_PF_GROUP_TRANSFER'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TRANSACTION_DEFINITION',
            'PRODUCT_FUNDS.CSC_PF_PRODUCT',
            '=',
            'TRANSACTION_DEFINITION.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BILLER_PRODUCT',
            'TRANSACTION_DEFINITION.CSC_TD_NAME',
            '=',
            'BILLER_PRODUCT.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS BILLER',
            'BILLER_PRODUCT.CSC_BP_BILLER',
            '=',
            'BILLER.CSC_BILLER_ID'
        )

        ->where(function ($query) use ($biller, $modul, $accountSrc, $accountDst, $type) {
            if (null != $modul) :
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
            endif;

            if (null != $biller) {
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', 'LIKE', '%'. $biller .'%');
            }

            if (null != $accountSrc) :
                $query->where('CSC_GTF_SOURCE', $accountSrc);
            endif;

            if (null != $accountDst) :
                $query->where('CSC_GTF_DESTINATION', $accountDst);
            endif;

            if (null != $type) :
                $query->where('CSC_GTF_TRANSFER_TYPE', $type);
            endif;
        })
        ->groupBy('CSC_GTF_ID')
        ->whereNull('CSC_GTF_DELETED_DT')
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);


        // Response Success
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Filter Data Group Transfer Funds Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Data Group Transfer Funds Not Found'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Filter data Group Transfer Funds Failed'
            );
        endif;
    }

    public function trash(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'modul' => ['required', 'string', 'max:100'],
                'biller' => ['required', 'string', 'max:50'],
                'items' => ['numeric', 'digits_between:1,3'],
                'account_src' => ['numeric', 'digits_between:1,20'],
                'account_dest' => ['numeric', 'digits_between:1,20'],
                'type' => ['numeric', 'digits:1'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;
        $modul = (false == $request->modul) ? null : $request->modul;
        $biller = (false == $request->biller) ? null : $request->biller;
        $accountSrc = $request->account_src;
        $accountDst = $request->account_dest;
        $type = $request->type;

        // Logic Get Data
        $data = CoreGroupTransferFunds::join(
            'CSCCORE_PRODUCT_FUNDS AS PRODUCT_FUNDS',
            'CSC_GTF_ID',
            '=',
            'PRODUCT_FUNDS.CSC_PF_GROUP_TRANSFER'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TRANSACTION_DEFINITION',
            'PRODUCT_FUNDS.CSC_PF_PRODUCT',
            '=',
            'TRANSACTION_DEFINITION.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BILLER_PRODUCT',
            'TRANSACTION_DEFINITION.CSC_TD_NAME',
            '=',
            'BILLER_PRODUCT.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS BILLER',
            'BILLER_PRODUCT.CSC_BP_BILLER',
            '=',
            'BILLER.CSC_BILLER_ID'
        )

        ->where(function ($query) use ($biller, $modul, $accountSrc, $accountDst, $type) {
            if (null != $modul) :
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
            endif;

            if (null != $biller) {
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', 'LIKE', '%'. $biller .'%');
            }

            if (null != $accountSrc) :
                $query->where('CSC_GTF_SOURCE', $accountSrc);
            endif;

            if (null != $accountDst) :
                $query->where('CSC_GTF_DESTINATION', $accountDst);
            endif;

            if (null != $type) :
                $query->where('CSC_GTF_TRANSFER_TYPE', $type);
            endif;
        })
        ->groupBy('CSC_GTF_ID')
        ->whereNotNull('CSC_GTF_DELETED_DT')
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);


        // Response Success
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Get Data Trash Group Transfer Funds Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Data Trash Group Transfer Funds Not Found'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Get data Trash Group Transfer Funds Failed'
            );
        endif;
    }

    public function deleteData(Request $request, $id)
    {
        if (null == $id) {
            $id = ['id' => 'The id Field Is Required'];

            return response()->json(
                new DataResponseResource(
                    400,
                    'Invalid Data Validation',
                    $id
                ),
                400
            );
        }

        try {
            $data = CoreGroupTransferFunds::where('CSC_GTF_ID', $id)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Group Transfer Funds Not Found'
                    ),
                    404
                );
            }

            CoreGroupTransferFunds::where('CSC_GTF_ID', $id)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete Group Transfer Funds Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete Group Transfer Funds Failed',
                    $th
                ),
                500
            );
        }
    }

    public function byBiller(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                ['biller_id' => ['required', 'string', 'max:5']]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Ini sialisasi Variable
        $billerId = (false == $request->biller_id) ? null : $request->biller_id;

        // Cek Biller
        if (null != $billerId) :
            $cekBiller = CoreBiller::searchData($billerId)->first();
            if (null == $cekBiller) :
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            endif;
        endif;

        // Logic Get Data By Biller
        $data = CoreGroupTransferFunds::getData()
        ->join(
            'CSCCORE_PRODUCT_FUNDS AS PF',
            'PF.CSC_PF_GROUP_TRANSFER',
            '=',
            'CSC_GTF_ID'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'TD.CSC_TD_NAME',
            '=',
            'PF.CSC_PF_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'BP.CSC_BP_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_BILLER AS BILLER',
            'BILLER.CSC_BILLER_ID',
            '=',
            'BP.CSC_BP_BILLER'
        )
        ->distinct()
        ->where(
            function ($query) use ($billerId) {
                if (null != $billerId) :
                    $query->where('BILLER.CSC_BILLER_ID', $billerId);
                endif;
            }
        )
        ->get(
            [
                'CSC_GTF_ID AS ID',
                'CSC_GTF_NAME AS NAME'
            ]
        );

        // Response Sukses
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Get List Group Transfer Funds By Biller Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Get List Group Transfer Funds By Biller Not Found',
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Get List Group Transfer Funds By Biller Failed',
            );
        endif;
    }

    public function getAmount(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    "group_transfer" => ['required', 'string', 'max:50'],
                    "settled_date" => ['required', 'date_format:Y-m-d'],
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
        $idGtf = $request->group_transfer;
        $settled = $request->settled_date;

        // Cek Data GTF
        $cekGtf = CoreGroupTransferFunds::searchData($idGtf)->first('CSC_GTF_ID AS ID');
        if (null == $cekGtf) :
            return $this->generalResponse(
                404,
                'Data Group Transfer Funds Not Found'
            );
        endif;

        // Logic Get Data
        $data = CoreGroupTransferFunds::getData()
        ->select(
            DB::raw('SUM(CSC_RDN_AMOUNT) AS AMOUNT'),
        )
        ->join(
            'CSCCORE_RECON_DANA AS DANA',
            'CSC_GTF_ID',
            '=',
            'DANA.CSC_RDN_GROUP_TRANSFER'
        )
        ->searchData($idGtf)
        ->where('DANA.CSC_RDN_SETTLED_DT', $settled)
        ->where(function ($query) {
            $query->where('DANA.CSC_RDN_STATUS', 3)
            ->orWhere('DANA.CSC_RDN_STATUS', 0);
        })
        ->get();

        // Response Sukses
        if (null != $data[0]->AMOUNT) :
            return response()->json(
                [
                    'result_code' => 200,
                    'result_message' => 'Get Data Amount Recon Dana Success',
                    'AMOUNT' => $data[0]->AMOUNT
                ]
            );
        endif;

        // Response Not Found
        if (null == $data[0]->AMOUNT) :
            return $this->generalResponse(
                404,
                'Get Data Amount Recon Dana Not Found'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Get Data Amount Recon Dana Failed'
            );
        endif;
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $count = count($id);
        $notFound = [];

        // Check Data
        for ($i=0; $i < $count; $i++) {
            $checkAccount = $this->groupTransferSearchDeletedData($id[$i]);

            // Validasi Data
            if (false == $checkAccount) :
                $notFound = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response  Not Found
        if (null == $count) :
            return $this->groupTransferNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->groupTransferSearchDeletedData($id[$n]);
                $data->CSC_GTF_DELETED_BY = null;
                $data->CSC_GTF_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Group Transfer Funds Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Group Transfer Funds Success But Some Data Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Group Transfer Funds Success');
    }
}
