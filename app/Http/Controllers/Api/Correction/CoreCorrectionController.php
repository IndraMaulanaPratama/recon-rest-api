<?php

namespace App\Http\Controllers\Api\Correction;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreCorrection;
use App\Models\CoreGroupOfProduct;
use App\Models\CoreGroupTransferFunds;
use App\Models\CoreProductFunds;
use App\Traits\CorrectionTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

use function PHPUnit\Framework\returnSelf;

class CoreCorrectionController extends Controller
{
    use CorrectionTraits;

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
            'CSC_CORR_ID AS ID',
            'CSC_CORR_GROUP_TRANSFER AS GROUP_TRANSFER',
            'CSC_CORR_CORRECTION AS CORRECTION',
            'CSC_CORR_CORRECTION_VALUE AS CORRECTION_VALUE',
            'CSC_CORR_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
            'CSC_CORR_DESC AS DESC',
        ];
    }

    public function getSimpleField()
    {
        return [
            'CORRECTION.CSC_CORR_ID AS ID',
            'BILLER.CSC_BILLER_NAME AS BILLER',
            'TRANSFER_FUNDS.CSC_GTF_NAME AS GROUP_TRANSFER',
            'CORRECTION.CSC_CORR_CORRECTION AS CORRECTION',
            'CORRECTION.CSC_CORR_CORRECTION_VALUE AS CORRECTION_VALUE',
            'CORRECTION.CSC_CORR_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
            'CORRECTION.CSC_CORR_DESC AS DESC',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_CORR_ID AS ID',
            'BILLER.CSC_BILLER_NAME AS BILLER',
            'TRANSFER_FUNDS.CSC_GTF_NAME AS GROUP_TRANSFER',
            'CSC_CORR_DATE AS DATE',
            'CSC_CORR_DATE_TRANSFER AS DATE_TRANSFER',
            'CSC_CORR_CORRECTION AS CORRECTION',
            'CSC_CORR_CORRECTION_VALUE AS CORRECTION_VALUE',
            'CSC_CORR_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
            'CSC_CORR_DATE_PINBUK AS DATE_PINBUK',
            'CSC_CORR_DESC AS DESC',
            'CSC_CORR_CREATED_DT AS CREATED',
            'CSC_CORR_CREATED_BY AS CREATED_BY',
            'CSC_CORR_STATUS AS STATUS',
        ];
    }

    public function index(Request $request, $config)
    {
        if (null == $config) {
            return response(
                new ResponseResource(400, 'Invalid Data Validation'),
                400,
                ['Accept' => 'Application/json']
            );
        }

        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;
        $modul = (false == $request->modul) ? null : $request->modul;
        $biller = (false == $request->biller) ? null : $request->biller;
        $group_transfer = (false == $request->group_transfer) ? null : $request->group_transfer;
        $date = $request->date;
        $date_transfer = $request->date_transfer;

        // Cek Modul
        if (null != $modul) :
            $cekModul = CoreGroupOfProduct::modul($modul)->first();
            if (null == $cekModul) :
                return $this->generalResponse(
                    404,
                    'Data Modul Not Found'
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

        // Cek Group Transfer
        if (null != $group_transfer) :
            $cekGroup = CoreGroupTransferFunds::searchData($group_transfer)->first();
            if (null == $cekGroup) :
                return $this->generalResponse(
                    404,
                    'Data Group Transfer Funds Not Found'
                );
            endif;
        endif;


        if ('simple' == $config) {
            $data = DB::connection('server_report')
            ->table('CSCCORE_CORRECTION AS CORRECTION')
            ->join(
                'CSCCORE_GROUP_TRANSFER_FUNDS AS TRANSFER_FUNDS',
                'CORRECTION.CSC_CORR_GROUP_TRANSFER',
                '=',
                'TRANSFER_FUNDS.CSC_GTF_ID'
            )
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
            // ->join(
            //     'CSCCORE_GROUP_OF_PRODUCT AS GOP',
            //     'GOP.CSC_GOP_PRODUCT_GROUP',
            //     '=',
            //     'BILLER.CSC_BILLER_GROUP_PRODUCT'
            // )
            ->where(function ($query) use ($request) {
                if (null != $request->modul) {
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $request->modul);
                }

                if (null != $request->group_transfer) {
                    $query->where('TRANSFER_FUNDS.CSC_GTF_ID', $request->group_transfer);
                }

                if (null != $request->biller) {
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $request->biller);
                }

                if (null != $request->date) {
                    $query->where('CORRECTION.CSC_CORR_DATE', $request->date);
                }

                if (null != $request->date_transfer) {
                    $query->where('CORRECTION.CSC_CORR_DATE_TRANSFER', $request->date_transfer);
                }

                $query->whereNull('CORRECTION.CSC_CORR_DELETED_DT');
            })
            ->groupBy('CORRECTION.CSC_CORR_ID')
            ->get(self::getSimpleField());


            if (count($data) >= 1) {
                return response(
                    new PaginateResponseResource(200, 'Get List Correction Success', $config, $data),
                    200,
                    ['Accept' => 'Application/json']
                );
            } elseif (count($data) < 1) {
                return response(
                    new ResponseResource(404, 'Data Correction Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } else {
                return response(
                    new DataResponseResource(500, 'Get List Correction Failed', $data),
                    500,
                    ['Accept' => 'Application/json']
                );
            }
        } elseif ('detail' == $config) {
            $data = CoreCorrection::join(
                'CSCCORE_GROUP_TRANSFER_FUNDS AS TRANSFER_FUNDS',
                'CSC_CORR_GROUP_TRANSFER',
                '=',
                'TRANSFER_FUNDS.CSC_GTF_ID'
            )
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
            ->where(function ($query) use ($modul, $biller, $group_transfer, $date, $date_transfer) {
                if (null != $modul) {
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
                }

                if (null != $group_transfer) {
                    $query->where('TRANSFER_FUNDS.CSC_GTF_ID', $group_transfer);
                }

                if (null != $biller) {
                    $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $biller);
                }

                if (null != $date) {
                    $query->where('CSC_CORR_DATE', $date);
                }

                if (null != $date_transfer) {
                    $query->where('CSC_CORR_DATE_TRANSFER', $date_transfer);
                }

                $query->whereNull('CSC_CORR_DELETED_DT');
            })
            ->groupBy('CSC_CORR_ID')
            // ->get(self::getPaginate());
            ->paginate(
                $items = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null != count($data)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Correction Success', $config, $data),
                    200,
                    ['Accept' => 'Application/json']
                );
            } elseif (null == count($data)) {
                return response(
                    new ResponseResource(404, 'Data Correction Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } else {
                return response(
                    new DataResponseResource(500, 'Get List Correction Failed', $data),
                    500,
                    ['Accept' => 'Application/json']
                );
            }
        } else {
            return response(
                new ResponseResource(400, 'Invalid Data Validation'),
                400,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'created_by' => ['required', 'string', 'max:50'],
                'group_transfer' => ['required', 'string', 'max:50'],
                'date_transfer' => ['required', 'date_format:Y-m-d'],
                'correction' => ['required', 'numeric'],
                'correction_value' => ['required', 'string', 'max:1'],
                'amount_transfer' => ['required', 'numeric'],
                'desc' => ['required', 'string', 'max:100'],
            ]);

            $cekTransferFunds = CoreGroupTransferFunds::searchData($request->group_transfer)
            ->first('CSC_GTF_ID AS TRANSFER_FUNDS');
            $cekProductFunds = CoreProductFunds::searchByAccount($request->group_transfer)
            ->first('CSC_PF_GROUP_TRANSFER AS GROUP_TRANSFER');

            if (null == $cekTransferFunds) {
                return response(
                    new ResponseResource(404, 'Data Group Transfer Funds Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            if (null == $cekProductFunds) {
                return response(
                    new ResponseResource(404, 'Data Product Transfer Funds Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }


            $statusDeleted = CoreCorrection::searchTrashData($request->id)
            ->first(self::getField());
            if (null == $statusDeleted) {
                $cekBiller = CoreCorrection::searchData($request->id)
                ->first();
                if (null == $cekBiller) {
                    $keterangan = null;
                    while ($keterangan == false) {
                        $id = Uuid::uuid4();
                        $cekId = CoreCorrection::searchData($id)->first();

                        if (null == $cekId) {
                            $keterangan = true;
                        } else {
                            $keterangan = false;
                        }
                    }
                    $clientId = $request->created_by;
                    $store = CoreCorrection::create([
                        'CSC_CORR_ID' => $id,
                        'CSC_CORR_GROUP_TRANSFER' => $request->group_transfer,
                        'CSC_CORR_DATE' => Carbon::now('Asia/Jakarta')->format('Y-m-d'),
                        'CSC_CORR_DATE_TRANSFER' => $request->date_transfer,
                        'CSC_CORR_CORRECTION' => $request->correction,
                        'CSC_CORR_CORRECTION_VALUE' => $request->correction_value,
                        'CSC_CORR_AMOUNT_TRANSFER' => $request->amount_transfer,
                        'CSC_CORR_DESC' => $request->desc,
                        'CSC_CORR_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                        'CSC_CORR_CREATED_BY' => $clientId,
                    ]);

                    if ($store) {
                        return response(
                            new ResponseResource(200, 'Insert Data Correction Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Insert Data Correction Failed', $store),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(409, 'Data Correction Exists'),
                    409,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(422, 'Unprocessable Entity'),
                422,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:36']]);

            // $data = CoreCorrection::searchData($request->id)
            // ->first(self::getField());

            $data = CoreCorrection::join(
                'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
                'GTF.CSC_GTF_ID',
                '=',
                'CSC_CORR_GROUP_TRANSFER'
            )
            ->join(
                'CSCCORE_PRODUCT_FUNDS AS PF',
                'PF.CSC_PF_GROUP_TRANSFER',
                '=',
                'GTF.CSC_GTF_ID'
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
                'CSCCORE_BILLER AS CB',
                'CB.CSC_BILLER_ID',
                '=',
                'BP.CSC_BP_BILLER'
            )
            // ->join(
            //     'CSCCORE_GROUP_OF_PRODUCT AS GOP',
            //     'GOP.CSC_GOP_PRODUCT_GROUP',
            //     '=',
            //     'CB.CSC_BILLER_GROUP_PRODUCT'
            // )
            ->searchData($request->id)
            ->first(
                [
                    'CSC_CORR_ID AS ID',
                    'CB.CSC_BILLER_GROUP_PRODUCT AS MODUL',
                    'CSC_BILLER_NAME AS BILLER',
                    'CSC_GTF_NAME AS GROUP_TRANSFER',
                    'CSC_CORR_DATE_TRANSFER AS DATE_TRANSFER',
                    'CSC_CORR_CORRECTION AS CORRECTION',
                    'CSC_CORR_CORRECTION_VALUE AS CORRECTION_VALUE',
                    'CSC_CORR_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
                    'CSC_CORR_DESC AS DESC',
                ]
            );

            if (null != $data) {
                return response(
                    new DataResponseResource(200, 'Get Data Correction Success', $data),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Correction Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'modified_by' => ['required', 'string', 'max:50'],
                    'group_transfer' => ['required', 'string', 'max:50'],
                    'date_transfer' => ['required', 'date_format:Y-m-d'],
                    'correction' => ['required', 'numeric'],
                    'correction_value' => ['required', 'string', 'max:1'],
                    'amount_transfer' => ['required', 'numeric'],
                    'desc' => ['required', 'string', 'max:100'],
                ]
            );

            $cekTransferFunds = CoreGroupTransferFunds::searchData($request->group_transfer)
            ->first('CSC_GTF_ID AS TRANSFER_FUNDS');
            $cekProductFunds = CoreProductFunds::searchByAccount($request->group_transfer)
            ->first('CSC_PF_GROUP_TRANSFER AS GROUP_TRANSFER');

            if (null == $cekTransferFunds) {
                return response(
                    new ResponseResource(404, 'Data Group Transfer Funds Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            if (null == $cekProductFunds) {
                return response(
                    new ResponseResource(404, 'Data Product Transfer Funds Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $data = CoreCorrection::searchData($id)->first();
            $clientId = $request->modified_by;

            if (null != $data) {
                $data->CSC_CORR_GROUP_TRANSFER = $request->group_transfer;
                $data->CSC_CORR_DATE_TRANSFER = $request->date_transfer;
                $data->CSC_CORR_CORRECTION = $request->correction;
                $data->CSC_CORR_CORRECTION_VALUE = $request->correction_value;
                $data->CSC_CORR_AMOUNT_TRANSFER = $request->amount_transfer;
                $data->CSC_CORR_DESC = $request->desc;
                $data->CSC_CORR_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $data->CSC_CORR_MODIFIED_BY = $clientId;
                $data->save();

                if ($data) {
                    return response(
                        new ResponseResource(200, 'Update Data Correction Success'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Update Data Correction Failed', $data),
                    500,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Correction Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
        }
    }

    public function destroy(Request $request, $id)
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

                $request->validate(
                    ['deleted_by' => ['required', 'string', 'max:50']]
                );

                $clientId = $request->deleted_by;
                $data = CoreCorrection::searchData($id)->first();

                if (null != $data) {
                    $data->CSC_CORR_DELETED_BY = $clientId;
                    $data->CSC_CORR_DELETED_DT = Carbon::now('Asia/Jakarta');
                    $data->save();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Correction Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Update Data Correction Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Correction Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } catch (ValidationException $th) {
                return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
            }
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }

    public function filter(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'modul' => ['required', 'string', 'max:100'],
                'biller' => ['required', 'string', 'max:50'],
                'group_transfer' => ['required', 'string', 'max:100'],
                'group_transfer_filter' => ['string', 'max:50'],
                'date' => ['date_format:Y-m-d'],
                'date_transfer' => ['date_format:Y-m-d'],
                'items' => ['numeric', 'digits_between:1,3'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;
        $modul = (false == $request->modul) ? null : $request->modul;
        $biller = (false == $request->biller) ? null : $request->biller;
        $group_transfer = (false == $request->group_transfer) ? null : $request->group_transfer;
        $group_transfer_filter = $request->group_transfer_filter;
        $date = $request->date;
        $date_transfer = $request->date_transfer;

        // Logic Get Data
        $data = CoreCorrection::join(
            'CSCCORE_GROUP_TRANSFER_FUNDS AS TRANSFER_FUNDS',
            'CSC_CORR_GROUP_TRANSFER',
            '=',
            'TRANSFER_FUNDS.CSC_GTF_ID'
        )
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
        ->where(function ($query) use (
            $modul,
            $biller,
            $group_transfer,
            $group_transfer_filter,
            $date,
            $date_transfer
        ) {
            if (null != $modul) {
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
            }

            if (null != $biller) {
                $query->where('BILLER.CSC_BILLER_ID', $biller);
            }

            if (null != $group_transfer) {
                $query->where('CSC_CORR_GROUP_TRANSFER', $group_transfer);
            }

            if (null != $group_transfer_filter) {
                $query->where('TRANSFER_FUNDS.CSC_GTF_NAME', $group_transfer_filter);
            }

            if (null != $date) {
                $query->where('CSC_CORR_DATE', $date);
            }

            if (null != $date_transfer) {
                $query->where('CSC_CORR_DATE_TRANSFER', $date_transfer);
            }

            $query->whereNull('CSC_CORR_DELETED_DT');
        })
        ->groupBy('CSC_CORR_ID')
        ->paginate(
            $items = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        if (null != count($data)) {
            return response()->json(
                new DataResponseResource(200, 'Filter Data Correction Success', $data),
                200,
            );
        } elseif (null == count($data)) {
            return response()->json(
                new ResponseResource(404, 'Filter Data Correction Not Found'),
                404,
            );
        }

        return response(
            new DataResponseResource(500, 'Filter Data Correction Failed', $data),
            500,
            ['Accept' => 'Application/json']
        );
    }

    public function trash(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'modul' => ['required', 'string', 'max:100'],
                'biller' => ['required', 'string', 'max:50'],
                'group_transfer' => ['required', 'string', 'max:100'],
                'group_transfer_filter' => ['string', 'max:50'],
                'date' => ['date_format:Y-m-d'],
                'date_transfer' => ['date_format:Y-m-d'],
                'items' => ['numeric', 'digits_between:1,3'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;
        $modul = (false == $request->modul) ? null : $request->modul;
        $biller = (false == $request->biller) ? null : $request->biller;
        $group_transfer = (false == $request->group_transfer) ? null : $request->group_transfer;
        $group_transfer_filter = $request->group_transfer_filter;
        $date = $request->date;
        $date_transfer = $request->date_transfer;

        // Logic Get Data
        $data = CoreCorrection::join(
            'CSCCORE_GROUP_TRANSFER_FUNDS AS TRANSFER_FUNDS',
            'CSC_CORR_GROUP_TRANSFER',
            '=',
            'TRANSFER_FUNDS.CSC_GTF_ID'
        )
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
        ->where(function ($query) use (
            $modul,
            $biller,
            $group_transfer,
            $group_transfer_filter,
            $date,
            $date_transfer
        ) {
            if (null != $modul) {
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modul);
            }

            if (null != $biller) {
                $query->where('BILLER.CSC_BILLER_ID', $biller);
            }

            if (null != $group_transfer) {
                $query->where('CSC_CORR_GROUP_TRANSFER', $group_transfer);
            }

            if (null != $group_transfer_filter) {
                $query->where('TRANSFER_FUNDS.CSC_GTF_NAME', $group_transfer_filter);
            }

            if (null != $date) {
                $query->where('CSC_CORR_DATE', $date);
            }

            if (null != $date_transfer) {
                $query->where('CSC_CORR_DATE_TRANSFER', $date_transfer);
            }

            $query->whereNotNull('CSC_CORR_DELETED_DT');
        })
        ->groupBy('CSC_CORR_ID')
        ->paginate(
            $items = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        if (null != count($data)) {
            return response()->json(
                new DataResponseResource(200, 'Get Data Trash Correction Success', $data),
                200,
            );
        } elseif (null == count($data)) {
            return response()->json(
                new ResponseResource(404, 'Data Trash Correction Not Found'),
                404,
            );
        }

        return response(
            new DataResponseResource(500, 'Get Data Trash Correction Failed', $data),
            500,
            ['Accept' => 'Application/json']
        );
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
            $data = CoreCorrection::where('CSC_CORR_ID', $id)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Correction Not Found'
                    ),
                    404
                );
            }

            CoreCorrection::where('CSC_CORR_ID', $id)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete Correction Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete Correction Failed',
                    $th
                ),
                500
            );
        }
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['string', 'max:36'],
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
            $checkAccount = $this->correctionSearchDeletedData($id[$i]);

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
            return $this->correctionNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->correctionSearchDeletedData($id[$n]);
                $data->CSC_CORR_DELETED_BY = null;
                $data->CSC_CORR_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Correction Fee Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Correction Fee Success But Some Data Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Correction Fee Success');
    }
}
