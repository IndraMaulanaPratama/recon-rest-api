<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreFormulaTransfer;
use App\Models\CoreProfileFee;
use App\Models\CoreProfileProduct;
use App\Models\TransactionDefinitionV2;
use App\Traits\ProfileTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CoreProfileFeeController extends Controller
{
    use ProfileTraits;

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
            'CSC_PROFILE_ID AS PROFILE_ID',
            'CSC_PROFILE_NAME AS PROFILE_NAME',
            'CSC_PROFILE_DESC AS PROFILE_DESC',
        ];
    }

    public function getPaginate()
    {
        $totalProduct = TransactionDefinitionV2::getData()->get('CSC_TD_NAME AS NAME');

        return [
            'CSC_PROFILE_ID AS PROFILE_ID',
            'CSC_PROFILE_NAME AS PROFILE_NAME',
            'CSC_PROFILE_DESC AS PROFILE_DESC',
            'CSC_PROFILE_DEFAULT AS DEFAULT',
            'CSC_PROFILE_CREATED_DT AS CREATED',
            'CSC_PROFILE_MODIFIED_DT AS MODIFIED',
            'CSC_PROFILE_CREATED_BY AS  CREATED_BY',
            'CSC_PROFILE_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        // Logic Simple Config
        if ('simple' == $config) :
            // Logic Get Data
            $data = CoreProfileFee::getData()->get(self::getField());

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get Data Profile Fee Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Profile Fee Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Data Profile Fee Failed'
                );
            }
        endif;

        // Logic Detail Config
        if ('detail' == $config) :
            // Inisialisasi Variable
            $items = (null != $request->items) ? $request->items : 10;
            $countProduct = TransactionDefinitionV2::getData()->count();

            // Logic Get Data
            $data = CoreProfileProduct::select(
                self::getPaginate(),
                DB::raw('COUNT(CSC_PP_PRODUCT) AS PROFILE_PRODUCT')
            )
            ->rightJoin(
                'CSCCORE_PROFILE_FEE AS PF',
                'PF.CSC_PROFILE_ID',
                '=',
                'CSC_PP_PROFILE'
            )
            ->whereNull(
                [
                    'PF.CSC_PROFILE_DELETED_DT',
                ]
            )
            ->groupBy('PF.CSC_PROFILE_ID')
            ->paginate(
                $items = $items
            );

            // Proses Maping data menggunakan pengulangan
            $countData = count($data);
            for ($i=0; $i < $countData; $i++) {
                // Menghitung jumlah data profile product yang terdaftar di table product
                $countProfile = CoreProfileProduct::select(
                    self::getPaginate(),
                    DB::raw('COUNT(CSC_PP_PRODUCT) AS PROFILE_PRODUCT')
                )
                ->join(
                    'CSCCORE_PROFILE_FEE AS PF',
                    'PF.CSC_PROFILE_ID',
                    '=',
                    'CSC_PP_PROFILE'
                )
                ->join(
                    'CSCCORE_TRANSACTION_DEFINITION AS TD',
                    'TD.CSC_TD_NAME',
                    '=',
                    'CSC_PP_PRODUCT'
                )
                ->where('CSC_PROFILE_ID', $data[$i]->PROFILE_ID)
                ->count();

                // Proses penambahan column PROFILE_PRODUCT dan TOTAL_PRODUCT
                $data[$i] = collect($data[$i]);
                $data[$i]->put('PROFILE_PRODUCT', $countProfile);
                $data[$i]->put('TOTAL_PRODUCT', $countProduct);
                $data[$i]->put('INDEX_NUMBER', $i);
            }

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get Data Profile Fee Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Profile Fee Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Data Profile Fee Failed'
                );
            }
        endif;

        // Logic Undefined Config
        if ('detail' != $config && 'simple' != $config) :
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        endif;
    }

    public function store(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'created_by' => ['required', 'string', 'max:50'],
                    'id' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:50'],
                    'desc' => ['required', 'string', 'max:100'],
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
        $clientId = $request->created_by;
        $id = $request->id;
        $name = $request->name;
        $desc = $request->desc;


        // Cek Profile exists
        $cekData = CoreProfileFee::searchData($id)->first(self::getField());
        if (null != $cekData) {
            return $this->generalResponse(
                409,
                'Data Profile Fee Exists'
            );
        }

        // Cek Status Deleted
        $statusDeleted = CoreProfileFee::searchTrashData($id)->first(self::getField());
        if (null != $statusDeleted) {
            return $this->generalResponse(
                422,
                'Unprocessable Entity'
            );
        }

        // Logic Add Data
        $store = CoreProfileFee::create([
            'CSC_PROFILE_ID' => $id,
            'CSC_PROFILE_NAME' => $name,
            'CSC_PROFILE_DESC' => $desc,
            'CSC_PROFILE_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            'CSC_PROFILE_CREATED_BY' => $clientId,
        ]);

        // Response Sukses
        if ($store) {
            return $this->generalResponse(
                200,
                'Insert Data Profile Fee Success'
            );
        }

        // Response Failed
        if (!$store) {
            return $this->generalResponse(
                500,
                'Insert Data Profile Fee Failed'
            );
        }
    }

    public function show(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(['id' => ['required', 'string', 'max:50']]);
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Logic Get Data
        $data = CoreProfileFee::searchData($request->id)->first(self::getField());

        // Response Sukses
        if (null != $data) {
            return $this->generalDataResponse(
                200,
                'Get Data Profile Fee Success',
                $data
            );
        }

        // Response Not Found
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get Data Data Profile Fee Failed'
            );
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi Data Mandatori
        try {
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:50'],
                'desc' => ['required', 'string', 'max:100'],
            ]);
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $clientId = $request->modified_by;
        $name = $request->name;
        $desc = $request->desc;

        // Logic Get Data Profile
        $data = CoreProfileFee::searchData($id)->first();

        // Cek Data Profile
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Logic Update Data
        $data->CSC_PROFILE_NAME = $name;
        $data->CSC_PROFILE_DESC = $desc;
        $data->CSC_PROFILE_MODIFIED_DT = Carbon::now('Asia/Jakarta');
        $data->CSC_PROFILE_MODIFIED_BY = $clientId;
        $data->save();

        // Response Sukses
        if ($data) {
            return $this->generalResponse(
                200,
                'Update Data Profile Fee Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Update Data Profile Fee Failed'
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        // Validasi Data Mandatori
        try {
            if (Str::length($id) > 50) {
                $id = ['The id must not be greater than 50 characters.'];

                return $this->generalDataResponse(
                    400,
                    'Invalid Data Validation',
                    $id
                );
            }

            $request->validate(
                ['deleted_by' => ['required', 'string', 'max:50']]
            );
        } catch (ValidationException $th) {
            return $this-> generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $clientId = $request->deleted_by;

        // Cek Data
        $data = CoreProfileFee::searchData($id)->first();
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Logic Delete Data
        $data->CSC_PROFILE_DELETED_BY = $clientId;
        $data->CSC_PROFILE_DELETED_DT = Carbon::now('Asia/Jakarta');
        $data->save();

        // Response Sukses
        if ($data) {
            return $this->generalResponse(
                200,
                'Delete Data Profile Fee Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Delete Data Profile Fee Failed'
            );
        }
    }

    public function filter(Request $request)
    {
        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;
        $countProduct = TransactionDefinitionV2::getData()->count();

        // Logic Get Data
        $data = CoreProfileProduct::select(
            self::getPaginate(),
            DB::raw('COUNT(CSC_PP_PRODUCT) AS PROFILE_PRODUCT')
        )
        ->rightJoin(
            'CSCCORE_PROFILE_FEE AS PF',
            'PF.CSC_PROFILE_ID',
            '=',
            'CSC_PP_PROFILE'
        )
        ->whereNull(
            [
                'PF.CSC_PROFILE_DELETED_DT',
            ]
        )
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_PROFILE_NAME', 'LIKE', '%'. $request->name.'%');
            }

            if (null != $request->id) {
                $query->where('CSC_PROFILE_ID', 'LIKE', '%'. $request->id.'%');
            }
        })
        ->groupBy('PF.CSC_PROFILE_ID')
        ->paginate(
            $items = $items
        );

        // Proses Maping data menggunakan pengulangan
        $countData = count($data);
        for ($i=0; $i < $countData; $i++) {
            // Menghitung jumlah data profile product yang terdaftar di table product
            $countProfile = CoreProfileProduct::select(
                self::getPaginate(),
                DB::raw('COUNT(CSC_PP_PRODUCT) AS PROFILE_PRODUCT')
            )
            ->join(
                'CSCCORE_PROFILE_FEE AS PF',
                'PF.CSC_PROFILE_ID',
                '=',
                'CSC_PP_PROFILE'
            )
            ->join(
                'CSCCORE_TRANSACTION_DEFINITION AS TD',
                'TD.CSC_TD_NAME',
                '=',
                'CSC_PP_PRODUCT'
            )
            ->where('CSC_PROFILE_ID', $data[$i]->PROFILE_ID)
            ->count();

            // Proses penambahan column PROFILE_PRODUCT dan TOTAL_PRODUCT
            $data[$i] = collect($data[$i]);
            $data[$i]->put('PROFILE_PRODUCT', $countProfile);
            $data[$i]->put('TOTAL_PRODUCT', $countProduct);
            $data[$i]->put('INDEX_NUMBER', $i);
        }

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Filter Data Profile Fee Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Filter Data Profile Fee Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Filter Data Profile Fee Failed'
            );
        }
    }

    public function trash(Request $request)
    {
        // Inisialisasi Varable
        $items = (null != $request->items) ? $request->items : 10;
        $countProduct = TransactionDefinitionV2::getData()->count();

        // Logic Get Data
        $data = CoreProfileProduct::select(
            self::getPaginate(),
            DB::raw('COUNT(CSC_PP_PRODUCT) AS PROFILE_PRODUCT')
        )
        ->join(
            'CSCCORE_PROFILE_FEE AS PF',
            'PF.CSC_PROFILE_ID',
            '=',
            'CSC_PP_PROFILE'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'TD.CSC_TD_NAME',
            '=',
            'CSC_PP_PRODUCT'
        )
        ->whereNotNull('PF.CSC_PROFILE_DELETED_DT')
        ->groupBy('CSC_PP_PROFILE')
        ->paginate(
            $items = $items
        );

        // Proses Maping data menggunakan pengulangan
        $countData = count($data);
        for ($i=0; $i < $countData; $i++) {
            // Menghitung jumlah data profile product yang terdaftar di table product
            $countProfile = CoreProfileProduct::select(
                self::getPaginate(),
                DB::raw('COUNT(CSC_PP_PRODUCT) AS PROFILE_PRODUCT')
            )
            ->join(
                'CSCCORE_PROFILE_FEE AS PF',
                'PF.CSC_PROFILE_ID',
                '=',
                'CSC_PP_PROFILE'
            )
            ->join(
                'CSCCORE_TRANSACTION_DEFINITION AS TD',
                'TD.CSC_TD_NAME',
                '=',
                'CSC_PP_PRODUCT'
            )
            ->whereNotNull('PF.CSC_PROFILE_DELETED_DT')
            ->where('CSC_PROFILE_ID', $data[$i]->PROFILE_ID)
            ->count();

            // Proses penambahan column PROFILE_PRODUCT dan TOTAL_PRODUCT
            $data[$i] = collect($data[$i]);
            $data[$i]->put('PROFILE_PRODUCT', $countProfile);
            $data[$i]->put('TOTAL_PRODUCT', $countProduct);
            $data[$i]->put('INDEX_NUMBER', $i);
        }

        // Response Sukses
        if (null != count($data)) {
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(
                200,
                'Get Trash Data Profile Fee Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Trash Profile Fee Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get Trash Data Profile Fee Failed'
            );
        }
    }

    public function getCountProduct(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(['id' => ['required', 'string', 'max:50']]);

            // Cek Profile
            $cekProfile = CoreProfileFee::searchData($request->id)->first('CSC_PROFILE_ID');
            if (null == $cekProfile) {
                return $this->generalResponse(
                    404,
                    'Data Profile Fee Not Found'
                );
            }

            // Logic Get Data
            $data = CoreProfileProduct::searchByProfile($request->id)
            ->count();

            // Response Sukses
            if (null != $data) {
                $response['result_code'] = 200;
                $response['result_message'] = 'Get Count Data Total Product Profile Fee Success';
                $response['total_product'] = $data;

                return response()->json($response, 200);
            }

            // Response Not Found
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Count Data Total Product Profile Fee Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Count Data Total Product Profile Fee Failed'
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

    public function getDataCopy(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(['id' => ['required', 'string', 'max:50']]);

            // Cek Data Profile
            $cekProfile = CoreProfileFee::searchData($request->id)->first('CSC_PROFILE_ID');
            if (null == $cekProfile) {
                return $this->generalResponse(
                    404,
                    'Data Profile Fee Not Found'
                );
            }

            // LOgic Get Header Data
            $header = CoreProfileFee::getData()
            ->join(
                'CSCCORE_PROFILE_PRODUCT AS PP',
                'PP.CSC_PP_PROFILE',
                '=',
                'CSC_PROFILE_ID'
            )
            ->where('CSC_PROFILE_ID', $request->id)
            ->first(
                [
                    'CSC_PROFILE_NAME AS NAME',
                    'CSC_PROFILE_DESC AS DESC',
                    'CSC_PROFILE_DEFAULT AS DEFAULT',
                    'CSC_PROFILE_DESC AS DESC',
                ]
            );

            // Logic Get Data Profile
            $data = CoreProfileFee::getData()
            ->join(
                'CSCCORE_PROFILE_PRODUCT AS PP',
                'PP.CSC_PP_PROFILE',
                '=',
                'CSC_PROFILE_ID'
            )
            ->where('CSC_PROFILE_ID', $request->id)
            ->get(
                [
                    'PP.CSC_PP_PRODUCT AS product',
                    'PP.CSC_PP_FEE_ADMIN AS admin',
                    'PP.CSC_PP_FEE_BILLER AS fee_biller',
                    'PP.CSC_PP_FEE_VSI AS fee_vsi',
                    'PP.CSC_PP_CLAIM_PARTNER AS claim_partner',
                    'PP.CSC_PP_CLAIM_VSI AS claim_vsi',
                    'PP.CSC_PP_MULTIPLIER_TYPE AS multiplier_type',
                    'PP.CSC_PP_FORMULA_TRANSFER AS formula_transfer',
                    'PP.CSC_PP_PARTNER_BILLING_TYPE AS partner_billing_type',
                    'PP.CSC_PP_BILLER_BILLING_TYPE AS biller_billing_type',
                ]
            );

            // Response Sukses
            if (null != $header || null != count($data)) {
                $response['NAME'] = $header->NAME;
                $response['DESC'] = $header->DESC;
                $response['DEFAULT'] = $header->DEFAULT;
                $response['data_product'] = $data;

                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get Data Copy Profile Fee Success',
                        $response
                    ),
                    200
                );
            }

            // Response Not Found
            if (null == $header || null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Copy Profile Fee Not Found'
                );
            }

            // Response Failed
            if (!$header || !$data) {
                return $this->generalResponse(
                    500,
                    'Get Data Copy Profile Fee Failed'
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

    public function copyData(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'created_by' => ['required', 'string', 'max:50'],
                    'id_pf' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:50'],
                    'desc' => ['required', 'string', 'max:100'],
                    'data_product' => ['array'],
                    'data_product.*.product' => ['string', 'max:100'],
                    'data_product.*.admin' => ['numeric'],
                    'data_product.*.fee_biller' => ['numeric'],
                    'data_product.*.fee_vsi' => ['numeric'],
                    'data_product.*.claim_partner' => ['numeric'],
                    'data_product.*.claim_vsi' => ['numeric'],
                    'data_product.*.multiplier_type' => ['numeric', 'digits:1'],
                    'data_product.*.formula_transfer' => ['numeric', 'digits_between:1,11'],
                    'data_product.*.partner_billing_type' => ['numeric', 'digits:1'],
                    'data_product.*.biller_billing_type' => ['numeric', 'digits:1'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Cek Data Profile Exists
        $cekProfile = CoreProfileFee::searchData($request->id_pf)->first('CSC_PROFILE_ID');
        if (null != $cekProfile) {
            return $this->generalResponse(
                409,
                'Data Profile Fee Exists'
            );
        }

        // Cek Deleted Status
        $cekDeleted = CoreProfileFee::searchTrashData($request->id_pf)->first('CSC_PROFILE_ID');
        if (null != $cekDeleted) {
            return $this->generalResponse(
                422,
                'Unprocessable Entity'
            );
        }

        // Inisialisasi Variable
        $clientId = $request->created_by;
        $id_pf = $request->id_pf;
        $name = $request->name;
        $desc = $request->desc;
        $dataProduct = $request->data_product;
        $countData = count($dataProduct);
        $warningExists = [];
        $warningProduct = [];
        $warningFormula = [];
        $storePP = [];

        // Logic Add Data
        $storePF = CoreProfileFee::create(
            [
                'CSC_PROFILE_ID' => $id_pf,
                'CSC_PROFILE_NAME' => $name,
                'CSC_PROFILE_DESC' => $desc,
                'CSC_PROFILE_CREATED_BY' => $clientId,
                'CSC_PROFILE_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            ]
        );

        // Response Failed
        if (!$storePF) {
            return $this->generalResponse(
                500,
                'Copy Data Profile Fee Failed',
            );
        }

        // Logic Copy Data
        for ($i=0; $i < $countData; $i++) {
            $product = $dataProduct[$i]['product'];
            $admin = $dataProduct[$i]['admin'];
            $feeBiller = $dataProduct[$i]['fee_biller'];
            $feeVsi = $dataProduct[$i]['fee_vsi'];
            $claimPartner = $dataProduct[$i]['claim_partner'];
            $claimVsi = $dataProduct[$i]['claim_vsi'];
            $multiplierType = $dataProduct[$i]['multiplier_type'];
            $formulaTransfer = $dataProduct[$i]['formula_transfer'];
            $partnerBillingType = $dataProduct[$i]['partner_billing_type'];
            $billerBillingType = $dataProduct[$i]['biller_billing_type'];

            // $cekExists[$i] = CoreProfileProduct::searchByProduct($product)->first('CSC_PP_ID');
            $cekProduct[$i] = TransactionDefinitionV2::searchData($product)->first('CSC_TD_NAME');
            $cekFormula[$i] = CoreFormulaTransfer::searchData($formulaTransfer)->first('CSC_FH_FORMULA');

            // Cek exists: Null = TRUE
            // Cek Product: Not Null = TRUE
            // Cek Formula: Not Null = TRUE

            // Cek
            // if (null != $cekProduct[$i] && null != $cekFormula[$i]) :
            //     $warningExists[] = $product;
            // endif;

            if (null == $cekProduct[$i] && null != $cekFormula[$i]) :
                // $warningExists[] = $product;
                $warningProduct[] = $product;
            endif;

            if (null != $cekProduct[$i] && null == $cekFormula[$i]) :
                // $warningExists[] = $product;
                $warningFormula[] = $formulaTransfer;
            endif;

            if (null == $cekProduct[$i] && null == $cekFormula[$i]) :
                // $warningExists[] = $product;
                $warningProduct[] = $product;
                $warningFormula[] = $formulaTransfer;
            endif;

            if (null != $cekProduct[$i] && null != $cekFormula[$i]) {
                // Handle Duplikat UUID
                $keterangan = null;
                while ($keterangan == false) {
                    $id = Uuid::uuid4();
                    $cekId = CoreProfileProduct::searchData($id)->first('CSC_PP_ID');

                    if (null == $cekId) {
                        $keterangan = true;
                    } else {
                        $keterangan = false;
                    }
                }

                // Logic Add Data
                $storePP = CoreProfileProduct::create(
                    [
                        'CSC_PP_ID' => $id,
                        'CSC_PP_PROFILE' => $request->id_pf,
                        'CSC_PP_PRODUCT' => $product,
                        'CSC_PP_FORMULA_TRANSFER' => $formulaTransfer,
                        'CSC_PP_FEE_ADMIN' => $admin,
                        'CSC_PP_FEE_BILLER' => $feeBiller,
                        'CSC_PP_FEE_VSI' => $feeVsi,
                        'CSC_PP_CLAIM_PARTNER' => $claimPartner,
                        'CSC_PP_CLAIM_VSI' => $claimVsi,
                        'CSC_PP_MULTIPLIER_TYPE' => $multiplierType,
                        'CSC_PP_PARTNER_BILLING_TYPE' => $partnerBillingType,
                        'CSC_PP_BILLER_BILLING_TYPE' => $billerBillingType,
                    ]
                );

                if (!$storePP) :
                    return response()->json(
                        new ResponseResource(
                            500,
                            'Copy Data Profile Fee Failed'
                        ),
                        500
                    );
                endif;
            }
        }

        // Response Product Exists
        // if (null != count($warningExists)
        //     && null == count($warningProduct)
        //     && null == count($warningFormula)
        // ) {
        //     $response = [
        //         'result_code' => 202,
        //         'result_message' => 'Copy Data Profile Fee Success But Some Product Exists',
        //         'product_exists' => $warningExists
        //     ];

        //     return response()->json(
        //         $response,
        //         202
        //     );
        // }

        // Response Cannot Processed
        if (null != count($warningFormula)
        && null != count($warningProduct)
        ) :
            $response = [
                'product_not_registered' => $warningProduct,
                'formula_not_registered' => $warningFormula,
            ];

            return $this->generalDataResponse(
                202,
                'Copy Data Profile Fee Success But Some Data Not Registered',
                $response
            );
        endif;

        if (null == count($warningFormula)
        && null != count($warningProduct)
        ) :
            $response = [
                'product_not_registered' => $warningProduct,
                'formula_not_registered' => $warningFormula,
            ];

            return $this->generalDataResponse(
                202,
                'Copy Data Profile Fee Success But Some Data Not Registered',
                $response
            );
        endif;

        // Response Cannot Process
        if (null != count($warningFormula)
        && null == count($warningProduct)
        ) :
            $response = [
                'product_not_registered' => $warningProduct,
                'formula_not_registered' => $warningFormula,
            ];

            return $this->generalDataResponse(
                202,
                'Copy Data Profile Fee Success But Some Data Not Registered',
                $response
            );
        endif;

        // Response Not Registered
        if (null != count($warningFormula)
        || null != count($warningProduct)
        ) :
            $response = [
                'product_not_registered' => $warningProduct,
                'formula_not_registered' => $warningFormula,
            ];

            return $this->generalDataResponse(
                202,
                'Copy Data Profile Fee Success But Some Data Not Registered',
                $response
            );
        endif;

        // Response Sukses
        if (null == count($warningFormula)
            && null == count($warningProduct)
        ) {
            return response()->json(
                new ResponseResource(
                    200,
                    'Copy Data Profile Fee Success'
                ),
                200
            );
        }
    }

    public function getListProductProfileFee(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(['profile' => ['required' , 'string', 'max:50']]);
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Cek Profile
        $cekProfile = CoreProfileFee::searchData($request->profile)->first('CSC_PROFILE_ID');
        if (null == $cekProfile) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Inisialisasi Variable
        $items = ($request->items == null) ? 10 : $request->items;
        $profile = $request->profile;

        // Logic Get Data
        $data = CoreProfileFee::searchData($profile)
        ->join(
            'CSCCORE_PROFILE_PRODUCT AS PP',
            'CSC_PROFILE_ID',
            '=',
            'PP.CSC_PP_PROFILE'
        )
        ->join(
            'CSCCORE_FORMULA_TRANSFER AS FT',
            'PP.CSC_PP_FORMULA_TRANSFER',
            '=',
            'FT.CSC_FH_ID'
        )
        ->paginate(
            $items = $items,
            $column = [
                "CSC_PP_ID AS ID",
                "CSC_PP_PROFILE AS PROFILE_ID",
                "CSC_PP_PRODUCT AS PRODUCT",
                "CSC_PP_FEE_ADMIN AS ADMIN",
                "CSC_PP_FEE_BILLER AS FEE_BILLER",
                "CSC_PP_FEE_VSI AS FEE_VSI",
                "CSC_PP_CLAIM_PARTNER AS CLAIM_PARTNER",
                "CSC_PP_CLAIM_VSI AS CLAIM_VSI",
                "CSC_PP_MULTIPLIER_TYPE AS MULTIPLIER_TYPE",
                "FT.CSC_FH_FORMULA AS FORMULA_TRANSFER",
                "CSC_PP_PARTNER_BILLING_TYPE AS PARTNER_BILLING_TYPE",
                "CSC_PP_BILLER_BILLING_TYPE AS BILLER_BILLING_TYPE"
            ]
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Get List Profile Fee Product Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Product Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get List Profile Fee Product Failed'
            );
        }
    }

    public function profileAddProduct(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'profile_id' => ['required', 'string', 'max:50'],
                    'product' => ['required', 'string', 'max:100'],
                    'admin' => ['required', 'numeric'],
                    'fee_biller' => ['required', 'numeric'],
                    'fee_vsi' => ['required', 'numeric'],
                    'claim_partner' => ['required', 'numeric'],
                    'claim_vsi' => ['required', 'numeric'],
                    'multiplier_type' => ['required', 'numeric', 'digits:1'],
                    'formula_transfer' => ['required', 'numeric', 'digits_between:1,11'],
                    'partner_billing_type' => ['required', 'numeric', 'digits:1'],
                    'biller_billing_type' => ['required', 'numeric', 'digits:1'],
                ]
            );

            /**
             * *** Validasi Fee Admin ***
             * --------------------------
             * 1. Field CSC_PP_FEE_BILLER + CSC_PP_FEE_VSI WAJIB <= CSC_PP_FEE_ADMIN. Artinya secara coding
             * Response 400 : Biller Fee Value + VSI Fee Value must not be greater than Admin Fee Value
             *
             * 2. Field CSC_PP_CLAIM_PARTNER + CSC_PP_CLAIM_VSI WAJIB = CSC_PP_FEE_VSI
             * Response 400 : Partner Claim Value + VSI Claim Value must be equal to VSI Fee Value
             */
            if ($request->admin > 0) :
                if (($request->fee_biller + $request->fee_vsi) > $request->admin) :
                    return $this->invalidValidation(
                        'Biller Fee Value + VSI Fee Value must not be greater than Admin Fee Value'
                    );
                endif;
            endif;

            if (($request->claim_partner + $request->claim_vsi) == $request->fee_vsi) :
                return $this->invalidValidation(
                    'Partner Claim Value + VSI Claim Value must be equal to VSI Fee Value'
                );
            endif;
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Cek Data Profile
        $cekProfile = CoreProfileFee::searchData($request->profile_id)->first('CSC_PROFILE_ID');
        if (null == $cekProfile) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Cek Data Profile Exists
        $cekExist = CoreProfileProduct::checkExistCopy(
            $request->profile_id,
            $request->product,
            $request->formula_transfer,
        )
        ->first();
        if (null != $cekExist) {
            return $this->generalResponse(
                409,
                'Data Profile Fee Product Exists'
            );
        }

        // Cek Data Product Exists
        // $cekProduct = CoreProfileProduct::searchByProduct($request->product)->first();
        // if (null != $cekProduct) {
        //     return $this->generalResponse(
        //         409,
        //         'Data Product/Area Exists'
        //     );
        // }

        // Cek Exists Product Profile
        $cekProduct = CoreProfileProduct::CheckExistProduct($request->profile_id, $request->product)->first();
        if (null != $cekProduct) {
            return $this->generalResponse(
                409,
                'Data Profile Fee Product Exists'
            );
        }

        // Cek Product Not Found
        $cekProduct2 = TransactionDefinitionV2::searchData($request->product)->first();
        if (null == $cekProduct2) {
            return $this->generalResponse(
                404,
                'Data Product/Area Not Found'
            );
        }

        // Cek Formula Transfer
        $cekFormulaTransfer = CoreFormulaTransfer::searchData($request->formula_transfer)->first();
        if (null == $cekFormulaTransfer) :
            return $this->generalResponse(
                404,
                'Data Formula Transfer Not Found'
            );
        endif;

        // Handle Duplikat UUID
        $keterangan = null;
        while ($keterangan == false) {
            $idProduct = Uuid::uuid4();
            $cekId = CoreProfileProduct::searchData($idProduct)
                ->first();

            if (null == $cekId) {
                $keterangan = true;
            } else {
                $keterangan = false;
            }
        }

        // Logic Add Data
        $data = CoreProfileProduct::create(
            [
                'CSC_PP_ID' => $idProduct,
                'CSC_PP_PROFILE'=> $request->profile_id,
                'CSC_PP_PRODUCT' => $request->product,
                'CSC_PP_FORMULA_TRANSFER' => $request->formula_transfer,
                'CSC_PP_FEE_ADMIN' => $request->admin,
                'CSC_PP_FEE_BILLER' => $request->fee_biller,
                'CSC_PP_FEE_VSI' => $request->fee_vsi,
                'CSC_PP_CLAIM_PARTNER' => $request->claim_partner,
                'CSC_PP_CLAIM_VSI' => $request->claim_vsi,
                'CSC_PP_MULTIPLIER_TYPE' => $request->multiplier_type,
                'CSC_PP_PARTNER_BILLING_TYPE' => $request->partner_billing_type,
                'CSC_PP_BILLER_BILLING_TYPE' => $request->biller_billing_type,
            ]
        );

        // Response Sukses
        if ($data) {
            return $this->generalResponse(
                200,
                'Insert Data Profile Fee Product Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Insert Data Profile Fee Product Failed'
            );
        }
    }

    public function getDataProduct(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(['id' => ['required', 'string', 'max:36']]);
        } catch (ValidationException$th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Logic Get Data
        $cekProfileProduct = CoreProfileProduct::searchData($request->id)->first(
            [
                'CSC_PP_ID AS ID',
                'CSC_PP_PROFILE AS PROFILE_ID',
                'CSC_PP_PRODUCT AS PRODUCT',
                'CSC_PP_FEE_ADMIN AS ADMIN',
                'CSC_PP_FEE_BILLER AS FEE_BILLER',
                'CSC_PP_FEE_VSI AS FEE_VSI',
                'CSC_PP_CLAIM_PARTNER AS CLAIM_PARTNER',
                'CSC_PP_CLAIM_VSI AS CLAIM_VSI',
                'CSC_PP_MULTIPLIER_TYPE AS MULTIPLIER_TYPE',
                'CSC_PP_FORMULA_TRANSFER AS FORMULA_TRANSFER',
                'CSC_PP_PARTNER_BILLING_TYPE AS PARTNER_BILLING_TYPE',
                'CSC_PP_BILLER_BILLING_TYPE AS BILLER_BILLING_TYPE',
            ]
        );

        // Cek Data Notfound
        if (null == $cekProfileProduct) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Product Not Found'
            );
        }

        // Response Sukses
        if (null != $cekProfileProduct) {
            return $this->generalDataResponse(
                200,
                'Get Data Profile Fee Product Success',
                $cekProfileProduct
            );
        }

        // Response Failed
        if (!$cekProfileProduct) {
            return $this->generalResponse(
                500,
                'Get Data Profile Fee Product Failed'
            );
        }
    }

    public function updateProductProfile(Request $request, $id)
    {
        // Validasi Data Mandatori
        try {
            if (Str::length($id) > 36) {
                $id = ['The id must not be greater than 36 characters.'];

                return $this->generalDataResponse(
                    400,
                    'Invalid Data Validation',
                    $id
                );
            }

            // Validasi Data Mandatori
            $request->validate(
                [
                    'product' => ['required', 'string', 'max:100'],
                    'admin' => ['required', 'numeric'],
                    'fee_biller' => ['required', 'numeric'],
                    'fee_vsi' => ['required', 'numeric'],
                    'claim_partner' => ['required', 'numeric'],
                    'claim_vsi' => ['required', 'numeric'],
                    'multiplier_type' => ['required', 'numeric', 'digits:1'],
                    'formula_transfer' => ['required', 'numeric', 'digits_between:1,11'],
                    'partner_billing_type' => ['required', 'numeric', 'digits:1'],
                    'biller_billing_type' => ['required', 'numeric', 'digits:1'],
                    ]
            );

            /**
             * *** Validasi Fee Admin ***
             * --------------------------
             * 1. Field CSC_PP_FEE_BILLER + CSC_PP_FEE_VSI WAJIB <= CSC_PP_FEE_ADMIN. Artinya secara coding
             * Response 400 : Biller Fee Value + VSI Fee Value must not be greater than Admin Fee Value
             *
             * 2. Field CSC_PP_CLAIM_PARTNER + CSC_PP_CLAIM_VSI WAJIB = CSC_PP_FEE_VSI
             * Response 400 : Partner Claim Value + VSI Claim Value must be equal to VSI Fee Value
             */
            if (($request->fee_biller + $request->fee_vsi) > $request->admin) :
                return $this->invalidValidation(
                    'Biller Fee Value + VSI Fee Value must not be greater than Admin Fee Value'
                );
            endif;

            if (($request->claim_partner + $request->claim_vsi) == $request->fee_vsi) :
                return $this->invalidValidation(
                    'Partner Claim Value + VSI Claim Value must be equal to VSI Fee Value'
                );
            endif;
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors(),
            );
        }

        // Cek Data Profile Product
        $data = CoreProfileProduct::searchData($id)->first();
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Product Not Found'
            );
        }

        // Cek Data Product
        $cekProduct = TransactionDefinitionV2::searchData($request->product)->first();
        if (null == $cekProduct) {
            return $this->generalResponse(
                404,
                'Data Product/Area Not Found'
            );
        }

        // Cek Data Profile Product Exist
        // $cekExist = CoreProfileProduct::checkExistProduct(
        //     $id,
        //     $request->product,
        // )
        // ->first();

        // Kondisi Ketika Data Product Dirubah
        // if (null != $cekExist) {
        //     if ($cekExist->CSC_PP_PRODUCT != $data->CSC_PP_PRODUCT) {
        //         $cekProductExists = CoreProfileProduct::searchByProduct($request->product)
        //         ->first();
        //         if (null != $cekProductExists) {
        //             return $this->generalResponse(
        //                 409,
        //                 'Data Product/Area Exists'
        //             );
        //         }
        //     }
        // } else {
        //     $cekProductExists = CoreProfileProduct::searchByProduct($request->product)
        //     ->first();
        //     if (null != $cekProductExists) {
        //         return $this->generalResponse(
        //             409,
        //             'Data Product/Area Exists'
        //         );
        //     }
        // }

        // Logic Update data
        $data->CSC_PP_PRODUCT = $request->product;
        $data->CSC_PP_FORMULA_TRANSFER = $request->formula_transfer;
        $data->CSC_PP_FEE_ADMIN = $request->admin;
        $data->CSC_PP_FEE_BILLER = $request->fee_biller;
        $data->CSC_PP_FEE_VSI = $request->fee_vsi;
        $data->CSC_PP_CLAIM_PARTNER = $request->claim_partner;
        $data->CSC_PP_CLAIM_VSI = $request->claim_vsi;
        $data->CSC_PP_MULTIPLIER_TYPE = $request->multiplier_type;
        $data->CSC_PP_PARTNER_BILLING_TYPE = $request->partner_billing_type;
        $data->CSC_PP_BILLER_BILLING_TYPE = $request->biller_billing_type;
        $data->save();

        // Response Sukses
        if ($data) {
            return $this->generalResponse(
                200,
                'Update Data Profile Fee Product Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Update Data Profile Fee Product Failed'
            );
        }
    }

    public function deleteProductProfile($id)
    {
        // Validasi Data Mandatori
        if (Str::length($id) > 36) {
            $id = ['The id must not be greater than 36 characters.'];

            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $id
            );
        }

        // Get Data
        $data = CoreProfileProduct::searchData($id)->first();

        // Response Not Found
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Product Not Found'
            );
        }

        // Logic Delete Data
        $data = $data->delete();

        // Response Sukses
        if ($data) {
            return $this->generalResponse(
                200,
                'Delete data Profile Fee Product Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Delete Data Profile Fee Product Failed'
            );
        }
    }

    public function deleteData(Request $request, $id)
    {
        // Cek Data Profile
        $data = CoreProfileFee::where('CSC_PROFILE_ID', $id)->first();
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Logic Delete Data
        $data = CoreProfileFee::where('CSC_PROFILE_ID', $id)->delete();

        // Response Sukses
        if ($data) {
            return $this->generalResponse(
                200,
                'Delete Profile Fee Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Delete Profile Fee Failed',
            );
        }
    }

    public function setDefault(Request $request, $id)
    {
        // Validasi Request ID
        if (Str::length($id) > 50) :
            $id = ['id' => 'The Id must not be greater than 50 characters.'];
            return $this->invalidValidation($id);
        endif;

        // Cek Data Profile
        $data = CoreProfileFee::searchData($id)->first();
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Profile Fee Not Found'
            );
        }

        // Cek Default Profile Fee
        $defaultProfile = CoreProfileFee::where('CSC_PROFILE_DEFAULT', 0)->first();
        if (null == $defaultProfile) {
            $statusDefault = 0;
        } else {
            $statusDefault = 1;
        }

        // Set Default Profile Fee
        try {
            // Inisialisasi Variable
            $clientId = $request->created_by;
            $default = $request->default;

            // Logic Set Default
            if (1 == $statusDefault) { // Kondisi ketika sudah ada default Profile
                $defaultProfile->CSC_PROFILE_DEFAULT = 1;
                $defaultProfile->CSC_PROFILE_MODIFIED_BY = $clientId;
                $defaultProfile->save();

                $data->CSC_PROFILE_DEFAULT = 0;
                $data->CSC_PROFILE_MODIFIED_BY = $clientId;
                $data->save();

                if ($data) {
                    $status = 200;
                    $response = 'Set Default Data Profile Fee Success';
                } else {
                    $status = 500;
                    $response = 'Set Default Data Profile Fee Failed';
                }
            } elseif (0 == $defaultProfile) { // Kondisi ketika belum ada default Profile
                $data->CSC_PROFILE_DEFAULT = 0;
                $data->CSC_PROFILE_MODIFIED_BY = $clientId;
                $data->save();

                if ($data) {
                    $status = 200;
                    $response = 'Set Default Data Profile Fee Success';
                } else {
                    $status = 500;
                    $response = 'Set Default Data Profile Fee Failed';
                }
            }

            // Throw Response
            return $this->generalResponse(
                $status,
                $response
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }
    }

    public function productUnexists(Request $request)
    {
        // Logic Get Data
        $data = TransactionDefinitionV2::getData()
        ->whereNotExists(function ($query) {
            $query->select('PP.CSC_PP_PRODUCT')
            ->from('CSCCORE_PROFILE_PRODUCT AS PP')
            ->whereColumn('PP.CSC_PP_PRODUCT', 'CSC_TD_NAME');
        })
        ->get('CSC_TD_NAME AS NAME');

        // Response Sukses
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Get List Profile Fee Product Unexists Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Get List Profile Fee Product Unexists Not Found'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Get List Profile Fee Product Unexists Failed'
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
            $checkAccount = $this->profileSearchDeletedData($id[$i]);

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
            return $this->profileNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->profileSearchDeletedData($id[$n]);
                $data->CSC_PROFILE_DELETED_BY = null;
                $data->CSC_PROFILE_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Profile Fee Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Profile Fee Success But Some Data Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Profile Fee Success');
    }
}
