<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleDefinition as ModelsModuleDefinition;
use App\Traits\ModuleDefinition as TraitsModuleDefinition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ModuleDefinition extends Controller
{
    use TraitsModuleDefinition;

    public function index(Request $request, $config)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'items' => ['numeric', 'digits_between:1,8'],
            ]);

            // Validasi Config
            if (Str::length($config) > 10) :
                $response['config'] = 'The config must not be greater than 10 characters.';
                return $this->invalidValidation($response);
            endif;
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $count = null;
        $items = (null == $request->items) ? 10 : $request->items;

        if ('simple' == $config) :
            // Logic Get Data
            try {
                $data = ModelsModuleDefinition::getData()
                ->get($this->moduleSimpleField());
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Get List Module Product/Area Failed', $th->getMessage());
            }

            // Hitung Jumlah Data
            $count = count($data);
        elseif ('detail' == $config) :
            // Logic get Data
            try {
                $data = ModelsModuleDefinition::getData()
                ->paginate($items, $this->moduleDetailField());
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Get List Module Product/Area Failed', $th->getMessage());
            }

            // Hitung Jumlah Data
            $count = count($data);

            // Add Index Number
            if (null != $count) :
                $data = $this->addIndexNumber($data);
            endif;
        endif;

        // Response Not Found
        if (null == $count) :
            return $this->moduleNotFound();
        endif;

        // Response Success
        return $this->generalConfigResponse(
            200,
            'Get List Module Product/Area Success',
            $config,
            $data
        );
    }

    public function store(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'group_name' => ['required', 'string', 'max:100'],
                'alias_name' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:100'],
                'biller_column' => ['required', 'string', 'max:100'],
                'table' => ['required', 'string', 'max:50'],
                'criteria' => ['required', 'string'],
                'find_criteria' => ['required', 'string'],
                'bank_criteria' => ['required', 'string', 'max:250'],
                'central_criteria' => ['required', 'string', 'max:250'],
                'bank' => ['required', 'string', 'max:100'],
                'central' => ['required', 'string', 'max:100'],
                'terminal' => ['required', 'string', 'max:100'],
                'subid' => ['required', 'string', 'max:100'],
                'subname' => ['required', 'string', 'max:100'],
                'switch_refnum' => ['required', 'string', 'max:100'],
                'switch_payment_refnum' => ['required', 'string', 'max:100'],
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
                'created_by' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $groupname = $request->group_name;
        $aliasName = $request->alias_name;
        $desc = $request->description;
        $billerColumn = $request->biller_column;
        $table = $request->table;
        $criteria = $request->criteria;
        $findCriteria = $request->find_criteria;
        $bankCriteria = $request->bank_criteria;
        $centralCriteria = $request->central_criteria;
        $bank = $request->bank;
        $central = $request->central;
        $terminal = $request->terminal;
        $subid = $request->subid;
        $subname = $request->subname;
        $switchRefnum = $request->switch_refnum;
        $switchPaymentRefnum = $request->switch_payment_refnum;
        $typeTransaction = $request->type_transaction;
        $date = $request->date;
        $nrek = $request->nrek;
        $nbill = $request->nbill;
        $billAmount = $request->bill_amount;
        $adminAmount = $request->admin_amount;
        $adminAmountDeduction0 = $request->admin_amount_deduction_0;
        $adminAmountDeduction1 = $request->admin_amount_deduction_1;
        $adminAmountDeduction2 = $request->admin_amount_deduction_2;
        $tableArchitecture = $request->table_arch;
        $bankGroupBy = $request->bank_group_by;
        $centralGroupBy = $request->central_group_by;
        $terminalGroupBy = $request->terminal_group_by;
        $isActive = 0;
        $createdBy = $request->created_by;
        $createdDt = Carbon::now('Asia/Jakarta');


        // Check Data Deleted
        $deleted = $this->moduleCheckDeletedData($groupname);

        // Response Data Deleted
        if (true == $deleted) :
            return $this->generalResponse(422, 'Unprocessable Entity');
        endif;

        // Check Data Exists
        $exists = $this->moduleCheckdata($groupname);

        // Response Data Exists
        if (true == $exists) :
            return $this->generalResponse(409, 'Data Module Product/Area Exists');
        endif;

        // Logic Add Data
        try {
            $field = [
                'CSC_MD_GROUPNAME' => $groupname,
                'CSC_MD_TABLE' => $table,
                'CSC_MD_ALIASNAME' => $aliasName,
                'CSC_MD_DESC' => $desc,
                'CSC_MD_BILLER_COLUMN' => $billerColumn,
                'CSC_MD_CRITERIA' => $criteria,
                'CSC_MD_FINDCRITERIA' => $findCriteria,
                'CSC_MD_BANK_CRITERIA' => $bankCriteria,
                'CSC_MD_CENTRAL_CRITERIA' => $centralCriteria,
                'CSC_MD_BANK_COLUMN' => $bank,
                'CSC_MD_CENTRAL_COLUMN' => $central,
                'CSC_MD_TERMINAL_COLUMN' => $terminal,
                'CSC_MD_SUBID_COLUMN' => $subid,
                'CSC_MD_SUBNAME_COLUMN' => $subname,
                'CSC_MD_SWITCH_REFNUM_COLUMN' => $switchRefnum,
                'CSC_MD_SWITCH_PAYMENT_REFNUM_COLUMN' => $switchPaymentRefnum,
                'CSC_MD_DATE_COLUMN' => $date,
                'CSC_MD_NREK_COLUMN' => $nrek,
                'CSC_MD_NBILL_COLUMN' => $nbill,
                'CSC_MD_BILL_AMOUNT_COLUMN' => $billAmount,
                'CSC_MD_ADM_AMOUNT_COLUMN' => $adminAmount,
                'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => $adminAmountDeduction0,
                'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => $adminAmountDeduction1,
                'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => $adminAmountDeduction2,
                'CSC_MD_TABLE_ARCH' => $tableArchitecture,
                'CSC_MD_BANK_GROUPBY' => $bankGroupBy,
                'CSC_MD_CENTRAL_GROUPBY' => $centralGroupBy,
                'CSC_MD_TERMINAL_GROUPBY' => $terminalGroupBy,
                'CSC_MD_TYPE_TRX' => $typeTransaction,
                'CSC_MD_ISACTIVE' => $isActive,
                'CSC_MD_CREATED_DT' => $createdBy,
                'CSC_MD_CREATED_By' => $createdDt,
            ];

            ModelsModuleDefinition::create($field);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Insert Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Insert Data Module Product/Area Success');
    }

    public function show(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'groupname' => ['required', 'string', 'max:100'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $groupname = $request->groupname;

        // Chcek Data Module
        $check = $this->moduleCheckData($groupname);

        // Response Module Not Found
        if (false == $check) :
            return $this->moduleNotFound();
        endif;

        // Logic Get Data Module
        try {
            $data = $this->moduleGetData($groupname, 'data');
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalDataResponse(200, 'Get Data Module Product/Area Success', $data);
    }

    public function update(Request $request, $groupname)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'alias_name' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:100'],
                'biller_column' => ['required', 'string', 'max:100'],
                'table' => ['required', 'string', 'max:50'],
                'criteria' => ['required', 'string'],
                'find_criteria' => ['required', 'string'],
                'bank_criteria' => ['required', 'string', 'max:250'],
                'central_criteria' => ['required', 'string', 'max:250'],
                'bank' => ['required', 'string', 'max:100'],
                'central' => ['required', 'string', 'max:100'],
                'terminal' => ['required', 'string', 'max:100'],
                'subid' => ['required', 'string', 'max:100'],
                'subname' => ['required', 'string', 'max:100'],
                'switch_refnum' => ['required', 'string', 'max:100'],
                'switch_payment_refnum' => ['required', 'string', 'max:100'],
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
                'modified_by' => ['required', 'string', 'max:50'],
            ]);

            if (Str::length($groupname) > 100) :
                $response['groupname'] = 'The groupname must not be greater than 100 characters.';
                return $this->invalidValidation($response);
            endif;
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $aliasName = $request->alias_name;
        $desc = $request->description;
        $billerColumn = $request->biller_column;
        $table = $request->table;
        $criteria = $request->criteria;
        $findCriteria = $request->find_criteria;
        $bankCriteria = $request->bank_criteria;
        $centralCriteria = $request->central_criteria;
        $bank = $request->bank;
        $central = $request->central;
        $terminal = $request->terminal;
        $subid = $request->subid;
        $subname = $request->subname;
        $switchRefnum = $request->switch_refnum;
        $switchPaymentRefnum = $request->switch_payment_refnum;
        $typeTransaction = $request->type_transaction;
        $date = $request->date;
        $nrek = $request->nrek;
        $nbill = $request->nbill;
        $billAmount = $request->bill_amount;
        $adminAmount = $request->admin_amount;
        $adminAmountDeduction0 = $request->admin_amount_deduction_0;
        $adminAmountDeduction1 = $request->admin_amount_deduction_1;
        $adminAmountDeduction2 = $request->admin_amount_deduction_2;
        $tableArch = $request->table_arch;
        $bankGroupBy = $request->bank_group_by;
        $centralGroupBy = $request->central_group_by;
        $terminalGroupBy = $request->terminal_group_by;
        $modifiedBy = $request->modified_by;
        $modifiedDt = Carbon::now('Asia/Jakarta');

        // Chcek Data Module
        $check = $this->moduleCheckData($groupname);

        // Response Module Not Found
        if (false == $check) :
            return $this->moduleNotFound();
        endif;

        // Logic Update Data
        try {
            $data = ModelsModuleDefinition::groupName($groupname)->first();
            $data->CSC_MD_TABLE = $table;
            $data->CSC_MD_ALIASNAME = $aliasName;
            $data->CSC_MD_DESC = $desc;
            $data->CSC_MD_BILLER_COLUMN = $billerColumn;
            $data->CSC_MD_CRITERIA = $criteria;
            $data->CSC_MD_FINDCRITERIA = $findCriteria;
            $data->CSC_MD_BANK_CRITERIA = $bankCriteria;
            $data->CSC_MD_CENTRAL_CRITERIA = $centralCriteria;
            $data->CSC_MD_BANK_COLUMN = $bank;
            $data->CSC_MD_CENTRAL_COLUMN = $central;
            $data->CSC_MD_TERMINAL_COLUMN = $terminal;
            $data->CSC_MD_SUBID_COLUMN = $subid;
            $data->CSC_MD_SUBNAME_COLUMN = $subname;
            $data->CSC_MD_SWITCH_REFNUM_COLUMN = $switchRefnum;
            $data->CSC_MD_SWITCH_PAYMENT_REFNUM_COLUMN = $switchPaymentRefnum;
            $data->CSC_MD_DATE_COLUMN = $date;
            $data->CSC_MD_NREK_COLUMN = $nrek;
            $data->CSC_MD_NBILL_COLUMN = $nbill;
            $data->CSC_MD_BILL_AMOUNT_COLUMN = $billAmount;
            $data->CSC_MD_ADM_AMOUNT_COLUMN = $adminAmount;
            $data->CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_0 = $adminAmountDeduction0;
            $data->CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_1 = $adminAmountDeduction1;
            $data->CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_2 = $adminAmountDeduction2;
            $data->CSC_MD_TABLE_ARCH = $tableArch;
            $data->CSC_MD_BANK_GROUPBY = $bankGroupBy;
            $data->CSC_MD_CENTRAL_GROUPBY = $centralGroupBy;
            $data->CSC_MD_TERMINAL_GROUPBY = $terminalGroupBy;
            $data->CSC_MD_TYPE_TRX = $typeTransaction;
            $data->CSC_MD_MODIFIED_DT = $modifiedDt;
            $data->CSC_MD_MODIFIED_BY = $modifiedBy;
            $data->save();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Update Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Update Data Module Product/Area Success');
    }

    public function destroy(Request $request, $groupname)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'deleted_by' => ['required', 'string', 'max:50'],
            ]);

            if (Str::length($groupname) > 100) :
                $response['groupname'] = 'The groupname must not be greater than 100 characters.';
                return $this->invalidValidation($response);
            endif;
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $deletedBy = $request->deleted_by;
        $deletedDt = Carbon::now('Asia/Jakarta');

        // Chcek Data Module
        $check = $this->moduleCheckData($groupname);

        // Response Module Not Found
        if (false == $check) :
            return $this->moduleNotFound();
        endif;

        // Logic Delete Data
        try {
            $data = ModelsModuleDefinition::groupName($groupname)->first();
            $data->CSC_MD_DELETED_BY = $deletedBy;
            $data->CSC_MD_DELETED_DT = $deletedDt;
            $data->save();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Delete Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Delete Data Module Product/Area Success');
    }

    public function dataColumn(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'table' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $table = $request->table;

        // Menampilkan List Table
        try {
            $listTable = DB::connection('server_recon')->select('show tables');
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data List Column name Table Failed', $th->getMessage());
        }

        // Mencari Table yang ditentukan
        $findTable = null;
        foreach ($listTable as $tables) {
            if ($tables->Tables_in_VSI_DEVEL_RECON == $request->table) {
                $findTable = $tables->Tables_in_VSI_DEVEL_RECON;
            }
        }

        // Handle Table tidak ditemukan
        if (false == $findTable) :
            return $this->responseNotFound('Data List Column Name Table Not Found');
        endif;

        // Handle Table ketika ditemukan
        if (false != $findTable) :
            // Membaca Data Kolom didalam table yang ditemukan
            try {
                $field = Schema::connection('server_recon')->getColumnListing($table);
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Get Data List Column name Table Failed', $th->getMessage());
            }

            return $this->generalDataResponse(200, 'Get Data List Column Name Table Success', $field);
        endif;
    }

    public function testData(Request $request)
    {
        // Validasi Data Mandatory
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
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $table = $request->table;
        $criteria = $request->criteria;
        $bankCriteria = $request->bank_criteria;
        $centralCriteria = $request->central_criteria;
        $bank = $request->bank;
        $central = $request->central;
        $nrek = $request->nrek;
        $nbill = $request->nbill;
        $billAmount = $request->bill_amount;
        $adminAmount = $request->admin_amount;
        $bankGroupBy = $request->bank_group_by;
        $centralgroupBy = $request->central_group_by;
        $dayMinOne = Carbon::now('Asia/Jakarta')->addDays(-1)->format('Y-m-d');
        $dollarSign = '$';

        $criteria = str_replace(["StartCCCC-MM-DD", "EndCCCC-MM-DD"], $dayMinOne, $criteria);
        $bankCriteria = str_replace($dollarSign."CA".$dollarSign, "('0000000')", $bankCriteria);
        $centralCriteria = str_replace($dollarSign."CID".$dollarSign, "('0000000')", $centralCriteria);

        // Logic Test Data
        try {
            $data = DB::connection('server_recon')
            ->table($table)
            ->select([$bank, $central])
            ->selectRaw($nrek)
            ->selectRaw($nbill)
            ->selectRaw($billAmount)
            ->selectRaw($adminAmount)
            ->whereRaw($criteria)
            ->whereRaw($bankCriteria)
            ->whereRaw($centralCriteria)
            ->first();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Test Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Test Data Module Product/Area Success', $data);
    }

    public function filter(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'groupname' => ['string', 'max:100'],
                'table' => ['string', 'max:50'],
                'bank' => ['string', 'max:100'],
                'central' => ['string', 'max:100'],
                'type_transaction' => ['string', 'max:100'],
                'isActive' => ['numeric', 'digits:1'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $groupname = $request->groupname;
        $table = $request->table;
        $bank = $request->bank;
        $central = $request->central;
        $typeTransaction = $request->type_transaction;
        $isActive = $request->isActive;
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Filter
        try {
            $params = [
                'groupname' => $groupname,
                'table' => $table,
                'bank' => $bank,
                'central' => $central,
                'type' => $typeTransaction,
                'isActive' => $isActive,
                'items' => $items
            ];
            $data = $this->moduleFilter($params, 'data');
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Filter Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Not Found
        if (false == $data) :
            return $this->responseNotFound('Filter Data Module Product/Area Not Found');
        endif;

        // Add Index Number
        $data = $this->addIndexNumber($data);
        // Response Success
        return $this->generalDataResponse(200, 'Get Filter Data Module Product/Area Success', $data);
    }

    public function trash(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'groupname' => ['string', 'max:100'],
                'table' => ['string', 'max:50'],
                'bank' => ['string', 'max:100'],
                'central' => ['string', 'max:100'],
                'type_transaction' => ['string', 'max:100'],
                'isActive' => ['numeric', 'digits:1'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $groupname = $request->groupname;
        $table = $request->table;
        $bank = $request->bank;
        $central = $request->central;
        $typeTransaction = $request->type_transaction;
        $isActive = $request->isActive;
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Filter
        try {
            $params = [
                'groupname' => $groupname,
                'table' => $table,
                'bank' => $bank,
                'central' => $central,
                'type' => $typeTransaction,
                'isActive' => $isActive,
                'items' => $items
            ];
            $data = $this->moduleFilter($params, 'trash');
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Trash Module Product/Area Failed', $th->getMessage());
        }

        // Response Not Found
        if (false == $data) :
            return $this->responseNotFound('Data Trash Module Product/Area Not Found');
        endif;

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->generalDataResponse(200, 'Get Data Trash Module Product/Area Success', $data);
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'groupname' => ['required', 'array', 'min:1'],
                'groupname.*' => ['string', 'max:100']
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $groupname = $request->groupname;
        $countGroupname = count($groupname);
        $notFound = [];

        // Get Data Trash
        try {
            for ($i=0; $i < $countGroupname; $i++) {
                $data[] = $this->moduleGetData($groupname[$i], 'trash');

                if (false == $data[$i]) :
                    $notFound[] = $groupname[$i];
                    unset($groupname[$i]);
                endif;
            }

            // Reordering & Recounting $name
            $groupname = array_values($groupname);
            $countGroupname = count($groupname);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Module Not Found
        if (false == $countGroupname) :
            return $this->moduleNotFound();
        endif;

        // Logic Update Data
        try {
            for ($i=0; $i < $countGroupname; $i++) {
                $data = $this->moduleGetData($groupname[$i], 'trash');
                $data->CSC_MD_DELETED_BY = null;
                $data->CSC_MD_DELETED_DT = null;
                $data->save();
            }
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Module Product/Area Failed', $th->getMessage());
        }

        // Response With Warning
        if (null != $notFound) :
            $response['module_not_found'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Module Product/Area Success But Some Module Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Module Product/Area Success');
    }

    public function deleteData($name)
    {
        // Validasi Data Mandatory
        if (Str::length($name) > 100) :
            $response['name'] = 'The groupname must not be greater than 100 characters.';
            return $this->invalidValidation($response);
        endif;

        // Check Data
        $data = $this->moduleCheckData($name);

        // Response Not Found
        if (false == $data) :
            return $this->moduleNotFound();
        endif;

        // Logic Delete Data
        try {
            ModelsModuleDefinition::where('CSC_MD_GROUPNAME', $name)->delete();
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Delete Data Module Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Delete Data Module Product/Area Success');
    }
}
