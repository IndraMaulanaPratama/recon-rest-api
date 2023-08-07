<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreTransactionDefinition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoreTransactionDefinitionController extends Controller
{
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
            'CSC_TD_NAME AS NAME',
            'CSC_TD_GROUPNAME AS GROUP_NAME',
            'CSC_TD_ALIASNAME AS ALIAS_NAME',
            'CSC_TD_DESC AS DESCRIPTION',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_TD_NAME AS NAME',
            'CSC_TD_GROUPNAME AS GROUP_NAME',
            'CSC_TD_ALIASNAME AS ALIAS_NAME',
            'CSC_TD_DESC AS DESCRIPTION',
            'CSC_TD_TABLE AS TABLE',
            'CSC_TD_CRITERIA AS CRITERIA',
            'CSC_TD_FINDCRITERIA AS FIND_CRITERIA',
            'CSC_TD_BANK_CRITERIA AS BANK_CRITERIA',
            'CSC_TD_BANK_COLUMN AS BANK_COLUMN',
            'CSC_TD_CENTRAL_COLUMN AS CENTRAL_COLUMN',
            'CSC_TD_TERMINAL_COLUMN AS TERMINAL_COLUMN',
            'CSC_TD_SWITCH_REFNUM_COLUMN AS SWITCH_REFNUM_COLUMN',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN AS SWITCH_PAYMENT_REFNUM_COLUMN',
            'CSC_TD_DATE_COLUMN AS DATE_COLUMN',
            'CSC_TD_NREK_COLUMN AS NREK_COLUMN',
            'CSC_TD_NBILL_COLUMN AS NBILL_COLUMN',
            'CSC_TD_BILL_AMOUNT_COLUMN AS BILL_AMOUNT_COLUMN',
            'CSC_TD_ADM_AMOUNT_COLUMN AS ADM_AMOUNT_COLUMN',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0 AS ADM_AMOUNT_COLUMN_DEDUCTION_0',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1 AS ADM_AMOUNT_COLUMN_DEDUCTION_1',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2 AS ADM_AMOUNT_COLUMN_DEDUCTION_2',
            'CSC_TD_TABLE_ARCH AS TABLE_ARCH',
            'CSC_TD_BANK_GROUPBY AS BANK_GROUPBY',
            'CSC_TD_CENTRAL_GROUPBY AS CENTRAL_GROUPBY',
            'CSC_TD_TERMINAL_GROUPBY AS TERMINAL_GROUPBY',
            'CSC_TD_TYPE_TRX AS TYPE_TRX',
            'CSC_TD_ISACTIVE AS ISACTIVE',
            'CSC_TD_CREATED_DT AS CREATED',
            'CSC_TD_MODIFIED_DT AS MODIFIED',
            'CSC_TD_CREATED_BY AS CREATED_BY',
            'CSC_TD_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        if ('simple' === $config) {
            $transaction = CoreTransactionDefinition::where('CSC_TD_DELETED_DT', null)->get(self::getField());

            if (null != count($transaction)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Product/Area Success', $config, $transaction),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Product/Area Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } elseif ('detail' === $config) {
            $transaction = CoreTransactionDefinition::getData()->paginate(
                $perPage = (null != $request->items) ? $request->items : 10,
                $column = self::getPaginate(),
            );

            if (null != count($transaction)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Product/Area Success', $config, $transaction),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Product/Area Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Product/Area Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
        $transaction = null;
    }

    public function store(Request $request)
    {
        $uniqueName = CoreTransactionDefinition::searchData($request->name)->get('CSC_TD_NAME AS name');

        try {
            $request->validate([
                'created_by' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:100'],
                'group_name' => ['required', 'string', 'max:100'],
                'alias_name' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:100'],
                'table' => ['required', 'string', 'max:50'],
                'criteria' => ['required', 'string', 'max:256'],
                'find_criteria' => ['required', 'string', 'max:100'],
                'bank_criteria' => ['required', 'string', 'max:250'],
                'central_criteria' => ['required', 'string', 'max:250'],
                'bank' => ['required', 'string', 'max:100'],
                'central' => ['required', 'string', 'max:100'],
                'terminal' => ['required', 'string', 'max:100'],
                'subid' => ['required', 'string', 'max:100'],
                'subname' => ['required', 'string', 'max:100'],
                'switch_refnum' => ['max:100'],
                'switch_payment_refnum' => ['max:100'],
                'type_transaction' => ['required', 'string', 'max:100'],
                'date' => ['required', 'string', 'max:100'],
                'nrek' => ['required', 'string', 'max:100'],
                'nbill' => ['required', 'string', 'max:100'],
                'bill_amount' => ['required', 'string', 'max:100'],
                'admin_amount' => ['required', 'string', 'max:100'],
                'admin_amount_deduction_0' => ['required', 'string', 'max:100'],
                'admin_amount_deduction_1' => ['required', 'string', 'max:100'],
                'admin_amount_deduction_2' => ['required', 'string', 'max:100'],
                'table_arch' => ['required', 'string', 'max:50'],
                'bank_group_by' => ['required', 'string', 'max:50'],
                'central_group_by' => ['required', 'string', 'max:50'],
                'terminal_group_by' => ['required', 'string', 'max:50'],
            ]);

            $cekData = CoreTransactionDefinition::searchData($request->name)->first();
            $statusDeleted = CoreTransactionDefinition::searchTrashData($request->name)->first(self::getField());
            $clientId = $request->created_by;

            if (null == $statusDeleted) {
                if (null == $cekData) {
                    $store = CoreTransactionDefinition::create([
                        'CSC_TD_NAME' => $request->name,
                        'CSC_TD_GROUPNAME' => $request->group_name,
                        'CSC_TD_ALIASNAME' => $request->alias_name,
                        'CSC_TD_DESC' => $request->description,
                        'CSC_TD_TABLE' => $request->table,
                        'CSC_TD_CRITERIA' => $request->criteria,
                        'CSC_TD_FINDCRITERIA' => $request->find_criteria,
                        'CSC_TD_BANK_CRITERIA' => $request->bank_criteria,
                        'CSC_TD_CENTRAL_CRITERIA' => $request->central_criteria,
                        'CSC_TD_BANK_COLUMN' => $request->bank,
                        'CSC_TD_CENTRAL_COLUMN' => $request->central,
                        'CSC_TD_TERMINAL_COLUMN' => $request->terminal,
                        'CSC_TD_SUBID_COLUMN' => $request->subid,
                        'CSC_TD_SUBNAME_COLUMN' => $request->subname,
                        'CSC_TD_SWITCH_REFNUM_COLUMN' => $request->switch_refnum,
                        'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => $request->switch_payment_refnum,
                        'CSC_TD_TYPE_TRX' => $request->type_transaction,
                        'CSC_TD_DATE_COLUMN' => $request->date,
                        'CSC_TD_NREK_COLUMN' => $request->nrek,
                        'CSC_TD_NBILL_COLUMN' => $request->nbill,
                        'CSC_TD_BILL_AMOUNT_COLUMN' => $request->bill_amount,
                        'CSC_TD_ADM_AMOUNT_COLUMN' => $request->admin_amount,
                        'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => $request->admin_amount_deduction_0,
                        'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => $request->admin_amount_deduction_1,
                        'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => $request->admin_amount_deduction_2,
                        'CSC_TD_TABLE_ARCH' => $request->table_arch,
                        'CSC_TD_BANK_GROUPBY' => $request->bank_group_by,
                        'CSC_TD_CENTRAL_GROUPBY' => $request->central_group_by,
                        'CSC_TD_TERMINAL_GROUPBY' => $request->terminal_group_by,
                        'CSC_TD_CREATED_BY' => $clientId,
                        'CSC_TD_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                    ]);

                    if ($store) {
                        return response(
                            new ResponseResource(200, 'Insert Data Product/Area Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Insert Data Product/Area Failed', $store),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(409, 'Data Product/Area Exists'),
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
            $request->validate(['name' => ['required', 'string', 'max:100']]);

            $transaction = CoreTransactionDefinition::searchData($request->name)->first(self::getPaginate());

            if (null != $transaction) {
                return response(
                    new DataResponseResource(200, 'Get Data Product/Area Success', $transaction),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Product/Area Not Found'),
                404,
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

    public function update(Request $request, $id)
    {
        $update = CoreTransactionDefinition::searchData($id)->first();

        if (null == $update) {
            return response(
                new ResponseResource(404, 'Data Product/Area Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }

        try {
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'group_name' => ['required', 'string', 'max:100'],
                'alias_name' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:100'],
                'table' => ['required', 'string', 'max:50'],
                'criteria' => ['required', 'string', 'max:256'],
                'find_criteria' => ['required', 'string', 'max:100'],
                'bank_criteria' => ['required', 'string', 'max:250'],
                'central_criteria' => ['required', 'string', 'max:250'],
                'bank' => ['required', 'string', 'max:100'],
                'central' => ['required', 'string', 'max:100'],
                'terminal' => ['required', 'string', 'max:100'],
                'subid' => ['required', 'string', 'max:100'],
                'subname' => ['required', 'string', 'max:100'],
                'switch_refnum' => ['max:100'],
                'switch_payment_refnum' => ['max:100'],
                'type_transaction' => ['required', 'string', 'max:100'],
                'date' => ['required', 'string', 'max:100'],
                'nrek' => ['required', 'string', 'max:100'],
                'nbill' => ['required', 'string', 'max:100'],
                'bill_amount' => ['required', 'string', 'max:100'],
                'admin_amount' => ['required', 'string', 'max:100'],
                'admin_amount_deduction_0' => ['required', 'string', 'max:100'],
                'admin_amount_deduction_1' => ['required', 'string', 'max:100'],
                'admin_amount_deduction_2' => ['required', 'string', 'max:100'],
                'table_arch' => ['required', 'string', 'max:50'],
                'bank_group_by' => ['required', 'string', 'max:50'],
                'central_group_by' => ['required', 'string', 'max:50'],
                'terminal_group_by' => ['required', 'string', 'max:50'],
            ]);

            $clientId = $request->modified_by;
            $update->CSC_TD_GROUPNAME = $request->group_name;
            $update->CSC_TD_ALIASNAME = $request->alias_name;
            $update->CSC_TD_DESC = $request->description;
            $update->CSC_TD_TABLE = $request->criteria;
            $update->CSC_TD_CRITERIA = $request->criteria;
            $update->CSC_TD_FINDCRITERIA = $request->find_criteria;
            $update->CSC_TD_BANK_CRITERIA = $request->bank_criteria;
            $update->CSC_TD_CENTRAL_CRITERIA = $request->central_criteria;
            $update->CSC_TD_BANK_COLUMN = $request->bank;
            $update->CSC_TD_CENTRAL_COLUMN = $request->central;
            $update->CSC_TD_TERMINAL_COLUMN = $request->terminal;
            $update->CSC_TD_SUBID_COLUMN = $request->subid;
            $update->CSC_TD_SUBNAME_COLUMN = $request->subname;
            $update->CSC_TD_SWITCH_REFNUM_COLUMN = $request->switch_refnum;
            $update->CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN = $request->switch_payment_refnum;
            $update->CSC_TD_TYPE_TRX = $request->type_transaction;
            $update->CSC_TD_DATE_COLUMN = $request->date;
            $update->CSC_TD_NREK_COLUMN = $request->nrek;
            $update->CSC_TD_NBILL_COLUMN = $request->nbill;
            $update->CSC_TD_BILL_AMOUNT_COLUMN = $request->bill_amount;
            $update->CSC_TD_ADM_AMOUNT_COLUMN = $request->admin_amount;
            $update->CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0 = $request->admin_amount_deduction_0;
            $update->CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1 = $request->admin_amount_deduction_1;
            $update->CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2 = $request->admin_amount_deduction_2;
            $update->CSC_TD_TABLE_ARCH = $request->table_arch;
            $update->CSC_TD_BANK_GROUPBY = $request->bank_group_by;
            $update->CSC_TD_CENTRAL_GROUPBY = $request->central_group_by;
            $update->CSC_TD_TERMINAL_GROUPBY = $request->terminal_group_by;
            $update->CSC_TD_MODIFIED_BY = $clientId;
            $update->CSC_TD_MODIFIED_DT = Carbon::now('Asia/Jakarta');
            $update->save();

            if ($update) {
                return response(
                    new ResponseResource(200, 'Update Data Product/Area Success'),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(500, 'Update Data Product/Area Failed'),
                500,
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

    public function destroy(Request $request, $name)
    {
        if (null != $name) {
            try {
                if (Str::length($name) > 100) {
                    $name = ['The name must not be greater than 100 characters.'];

                    return response(
                        new DataResponseResource(400, 'Invalid Data Validation', $name),
                        400,
                        ['Accept' => 'application/json']
                    );
                }

                $data = CoreTransactionDefinition::searchData($name)->first();
                $request->validate(['deleted_by' => ['required', 'string', 'max:50'],]);
                $clientId = $request->deleted_by;

                if (null != $data) {
                    $data->CSC_TD_DELETED_BY = $clientId;
                    $data->CSC_TD_DELETED_DT = Carbon::now('Asia/Jakarta');
                    $data->save();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Product/Area Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Delete Data Product/Area Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Product/Area Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } catch (ValidationException $th) {
                return response()->json(
                    new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                    400
                );
            }
        } else {
            return response()->json(
                new ResponseResource(400, 'Invalid Data Validation'),
                400
            );
        }
    }

    public function filter(Request $request)
    {
        $product = CoreTransactionDefinition::getData()
            ->where(function ($query) use ($request) {
                if ('' != $request->name) {
                    $query->where('CSC_TD_NAME', 'LIKE', '%' .$request->name. '%');
                }

                if ('' != $request->table) {
                    $query->where('CSC_TD_TABLE', 'LIKE', '%'.$request->table.'%');
                }

                if ('' != $request->bank) {
                    $query->where('CSC_TD_BANK_COLUMN', 'LIKE', '%'.$request->bank.'%');
                }

                if ('' != $request->central) {
                    $query->where('CSC_TD_CENTRAL_COLUMN', 'LIKE', '%'.$request->central.'%');
                }

                if ('' != $request->type_transaction) {
                    $query->where('CSC_TD_TYPE_TRX', 'LIKE', '%'.$request->type_transaction.'%');
                }

                if ('' != $request->isActive) {
                    $query->where('CSC_TD_ISACTIVE', $request->isActive);
                }
            })

            ->paginate(
                $perpage = (null != $request->items) ? $request->items : 10,
                $column = self::getPaginate()
            )
        ;

        if (count($product) >= 1) {
            return response(
                new DataResponseResource(200, 'Filter Data Product/Area Success', $product),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Filter Data Product/Area Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function trash(Request $request)
    {
        $product = CoreTransactionDefinition::getTrashData()
        ->where(function ($query) use ($request) {
            if ('' != $request->name) {
                $query->where('CSC_TD_NAME', 'LIKE', '%'.$request->name.'%');
            }

            if ('' != $request->table) {
                $query->where('CSC_TD_TABLE', 'LIKE', '%'.$request->table.'%');
            }

            if ('' != $request->bank) {
                $query->where('CSC_TD_BANK_COLUMN', 'LIKE', '%'.$request->bank.'%');
            }

            if ('' != $request->central) {
                $query->where('CSC_TD_CENTRAL_COLUMN', 'LIKE', '%'.$request->central.'%');
            }

            if ('' != $request->type_transaction) {
                $query->where('CSC_TD_TYPE_TRX', 'LIKE', '%'.$request->type_transaction.'%');
            }

            if ('' != $request->isActive) {
                $query->where('CSC_TD_ISACTIVE', $request->isActive);
            }
        })

        ->paginate(
            $perpage = (null != $request->items) ? $request->items : 10,
            $column = self::getPaginate()
        );

        if (count($product) >= 1) {
            // Add Index Number
            $product = $this->addIndexNumber($product);

            return response(
                new DataResponseResource(200, 'Get Data Trash Product Success', $product),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Get Data Trash Product Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function getCount()
    {
        $countProduct = CoreTransactionDefinition::getData()->count();

        if ($countProduct != null) {
            return response(
                [
                    'result_code' => 200,
                    'result_message' => 'Get Count Total Product Success',
                    'total_product' => $countProduct,
                ],
                200,
                ['Accept' => 'Application/json']
            );
        } elseif ($countProduct == null) {
            return response(
                new ResponseResource(
                    404,
                    'Get Count Total Product Not Found',
                ),
                404,
                ['Accept' => 'Application/json']
            );
        } else {
            return response(
                [
                    'result_code' => 200,
                    'result_message' => 'Get Count Total Product Failed',
                    'total_product' => $countProduct,
                ],
                500,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function dataColumn(Request $request)
    {
        try {
            $request->validate(['table' => ['required', 'string', 'max:50']]);

            $listTable = DB::connection('server_recon')->select('show tables');

            $findTable = null;
            foreach ($listTable as $tables) {
                if ($tables->Tables_in_VSI_DEVEL_RECON == $request->table) {
                    $findTable = $tables->Tables_in_VSI_DEVEL_RECON;
                }
            }

            if ($findTable == true) {
                $getField = Schema::connection('server_recon')->getColumnListing($findTable);
                return response(
                    new DataResponseResource(200, 'Get Data List Column Name Table Success', $getField),
                    200,
                    ['Accept' => 'Application/json']
                );
            } elseif ($findTable == false) {
                return response(
                    new ResponseResource(404, 'Data List Column Name Table Not Found', $findTable),
                    404,
                    ['Accept' => 'Application/json']
                );
            } else {
                return response(
                    new DataResponseResource(500, 'Data List Column Name Table Failed', $findTable),
                    500,
                    ['Accept' => 'Application/json']
                );
            }
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function testData(Request $request)
    {
        try {
            $request->validate(
                [
                    'table' => ['required', 'string', 'max:50'],
                    'criteria' => ['required', 'string', 'max:256'],
                    'bank_criteria' => ['required', 'string', 'max:250'],
                    'central_criteria' => ['required', 'string', 'max:250'],
                    'bank' => ['required', 'string', 'max:100'],
                    'central' => ['required', 'string', 'max:100'],
                    'nrek' => ['required', 'string', 'max:100'],
                    'nbill' => ['required', 'string', 'max:100'],
                    'bill_amount' => ['required', 'string', 'max:100'],
                    'admin_amount' => ['required', 'string', 'max:100'],
                    'bank_group_by' => ['required', 'string', 'max:50'],
                    'central_group_by' => ['required', 'string', 'max:50'],
                ]
            );


            $dayMinOne = Carbon::now('Asia/Jakarta')->addDays(-1)->format('Y-m-d');
            $dollarSign = '$';

            $criteria = str_replace(["StartCCCC-MM-DD", "EndCCCC-MM-DD"], $dayMinOne, $request->criteria);
            $bankCriteria = str_replace($dollarSign."CA".$dollarSign, "('0000000')", $request->bank_criteria);
            $centralCriteria = str_replace($dollarSign."CID".$dollarSign, "('0000000')", $request->central_criteria);

            try {
                $data = DB::connection('server_recon')
                ->table($request->table)
                ->select([$request->bank, $request->central])
                ->selectRaw($request->nrek)
                ->selectRaw($request->nbill)
                ->selectRaw($request->bill_amount)
                ->selectRaw($request->admin_amount)
                ->whereRaw($criteria)
                ->whereRaw($bankCriteria)
                ->whereRaw($centralCriteria)
                ->groupBy($request->central)
                ->first();

                if ($data || $data == null) {
                    return response(
                        new ResponseResource(200, 'Test Data Product/Area Success'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }
            } catch (\Throwable $th) {
                return response(
                    new ResponseResource(500, 'Test Data Product/Area Failed'),
                    500,
                    ['Accept' => 'Application/json']
                );
            }
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function deleteData(Request $request, $name)
    {
        if (null == $name) {
            $name = ['name' => 'The name Field Is Required'];

            return response()->json(
                new DataResponseResource(
                    400,
                    'Invalid Data Validation',
                    $name
                ),
                400
            );
        }

        try {
            $data = CoreTransactionDefinition::where('CSC_TD_NAME', $name)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Product/Area Not Found'
                    ),
                    404
                );
            }

            CoreTransactionDefinition::where('CSC_TD_NAME', $name)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete Product/Area Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete Product/Area Failed',
                    $th
                ),
                500
            );
        }
    }
}
