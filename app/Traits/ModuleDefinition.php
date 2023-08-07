<?php

namespace App\Traits;

use App\Models\ModuleDefinition as ModelsModuleDefinition;

trait ModuleDefinition
{
    use ResponseHandler;

    // Set Simple Field
    public function moduleSimpleField()
    {
        return [
            'CSC_MD_GROUPNAME AS GROUP_NAME',
            'CSC_MD_TABLE AS TABLE',
            'CSC_MD_DESC AS DESCRIPTION',
            'CSC_MD_BILLER_COLUMN AS BILLER_COLUMN',
        ];
    }

    // Set Simple Field
    public function moduleDetailField()
    {
        return [
            'CSC_MD_GROUPNAME AS GROUP_NAME',
            'CSC_MD_ALIASNAME AS ALIAS_NAME',
            'CSC_MD_DESC AS DESCRIPTION',
            'CSC_MD_BILLER_COLUMN AS BILLER_COLUMN',
            'CSC_MD_TABLE AS TABLE',
            'CSC_MD_CRITERIA AS CRITERIA',
            'CSC_MD_FINDCRITERIA AS FIND_CRITERIA',
            'CSC_MD_BANK_CRITERIA AS BANK_CRITERIA',
            'CSC_MD_CENTRAL_CRITERIA AS CENTRAL_CRITERIA',
            'CSC_MD_BANK_COLUMN AS BANK_COLUMN',
            'CSC_MD_CENTRAL_COLUMN AS CENTRAL_COLUMN',
            'CSC_MD_TERMINAL_COLUMN AS TERMINAL_COLUMN',
            'CSC_MD_SUBID_COLUMN AS SUBID_COLUMN',
            'CSC_MD_SUBNAME_COLUMN AS SUBNAME_COLUMN',
            'CSC_MD_SWITCH_REFNUM_COLUMN AS SWITCH_REFNUM_COLUMN',
            'CSC_MD_SWITCH_PAYMENT_REFNUM_COLUMN AS SWITCH_PAYMENT_REFNUM_COLUMN',
            'CSC_MD_DATE_COLUMN AS DATE_COLUMN',
            'CSC_MD_NREK_COLUMN AS NREK_COLUMN',
            'CSC_MD_NBILL_COLUMN AS NBILL_COLUMN',
            'CSC_MD_BILL_AMOUNT_COLUMN AS BILL_AMOUNT_COLUMN',
            'CSC_MD_ADM_AMOUNT_COLUMN AS ADM_AMOUNT_COLUMN',
            'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_0 AS ADM_AMOUNT_COLUMN_DEDUCTION_0',
            'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_1 AS ADM_AMOUNT_COLUMN_DEDUCTION_1',
            'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_2 AS ADM_AMOUNT_COLUMN_DEDUCTION_2',
            'CSC_MD_TABLE_ARCH AS TABLE_ARCH',
            'CSC_MD_BANK_GROUPBY AS BANK_GROUPBY',
            'CSC_MD_CENTRAL_GROUPBY AS CENTRAL_GROUPBY',
            'CSC_MD_TERMINAL_GROUPBY AS TERMINAL_GROUPBY',
            'CSC_MD_TYPE_TRX AS TYPE_TRX',
            'CSC_MD_ISACTIVE AS ISACTIVE',
            'CSC_MD_CREATED_DT AS CREATED',
            'CSC_MD_CREATED_BY AS CREATED_BY',
            'CSC_MD_MODIFIED_DT AS MODIFIED',
            'CSC_MD_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    // Set Get Data Field
    public function moduleGetDataField()
    {
        return [
            'CSC_MD_GROUPNAME AS GROUP_NAME',
            'CSC_MD_ALIASNAME AS ALIAS_NAME',
            'CSC_MD_DESC AS DESCRIPTION',
            'CSC_MD_BILLER_COLUMN AS BILLER_COLUMN',
            'CSC_MD_TABLE AS TABLE',
            'CSC_MD_CRITERIA AS CRITERIA',
            'CSC_MD_FINDCRITERIA AS FIND_CRITERIA',
            'CSC_MD_BANK_CRITERIA AS BANK_CRITERIA',
            'CSC_MD_CENTRAL_CRITERIA AS CENTRAL_CRITERIA',
            'CSC_MD_BANK_COLUMN AS BANK_COLUMN',
            'CSC_MD_CENTRAL_COLUMN AS CENTRAL_COLUMN',
            'CSC_MD_TERMINAL_COLUMN AS TERMINAL_COLUMN',
            'CSC_MD_SUBID_COLUMN AS SUBID_COLUMN',
            'CSC_MD_SUBNAME_COLUMN AS SUBNAME_COLUMN',
            'CSC_MD_SWITCH_REFNUM_COLUMN AS SWITCH_REFNUM_COLUMN',
            'CSC_MD_SWITCH_PAYMENT_REFNUM_COLUMN AS SWITCH_PAYMENT_REFNUM_COLUMN',
            'CSC_MD_DATE_COLUMN AS DATE_COLUMN',
            'CSC_MD_NREK_COLUMN AS NREK_COLUMN',
            'CSC_MD_NBILL_COLUMN AS NBILL_COLUMN',
            'CSC_MD_BILL_AMOUNT_COLUMN AS BILL_AMOUNT_COLUMN',
            'CSC_MD_ADM_AMOUNT_COLUMN AS ADM_AMOUNT_COLUMN',
            'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_0 AS ADM_AMOUNT_COLUMN_DEDUCTION_0',
            'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_1 AS ADM_AMOUNT_COLUMN_DEDUCTION_1',
            'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_2 AS ADM_AMOUNT_COLUMN_DEDUCTION_2',
            'CSC_MD_TABLE_ARCH AS TABLE_ARCH',
            'CSC_MD_BANK_GROUPBY AS BANK_GROUPBY',
            'CSC_MD_CENTRAL_GROUPBY AS CENTRAL_GROUPBY',
            'CSC_MD_TERMINAL_GROUPBY AS TERMINAL_GROUPBY',
            'CSC_MD_TYPE_TRX AS TYPE_TRX',
        ];
    }

    // Set Response Module Not Found
    public function moduleNotFound()
    {
        return $this->responseNotFound('Data Module Product/Area Not Found');
    }

    // Check Data Module
    public function moduleCheckData($name)
    {
        // Logic Get data
        $data = ModelsModuleDefinition::groupName($name)->first('CSC_MD_GROUPNAME');

        // Null = false | !Null == data
        return (null == $data) ? false : true;
    }

    // Check Data Deleted
    public function moduleCheckDeletedData($name)
    {
        // Logic Get data
        $data = ModelsModuleDefinition::trashGroupName($name)->first('CSC_MD_GROUPNAME');

        // Null = false | !Null == true
        return (null == $data) ? false : true;
    }

    // Get Data Module
    public function moduleGetData($groupname, $type)
    {
        // Logic Get Data
        if ('data' == $type) :
            $data = ModelsModuleDefinition::groupName($groupname)
            ->first($this->moduleGetDataField());
        elseif ('trash' == $type) :
            $data = ModelsModuleDefinition::select(
                'CSC_MD_GROUPNAME',
                'CSC_MD_DELETED_BY',
                'CSC_MD_DELETED_DT'
            )
            ->where('CSC_MD_GROUPNAME', $groupname)
            ->whereNotNull('CSC_MD_DELETED_DT')
            ->first();
        endif;

        // Null == False | !Null == Data
        return (null == $data) ? false : $data;
    }

    // Filter Module
    public function moduleFilter($params, $type)
    {
        // Logic Get Data
        if ('data' == $type) :
            $data = ModelsModuleDefinition::getData()
            ->where(function ($query) use ($params) {
                if (null != $params['groupname']) :
                    $query->getData()->filterGroupName($params['groupname']);
                endif;

                if (null != $params['table']) :
                    $query->getData()->filterTable($params['table']);
                endif;

                if (null != $params['bank']) :
                    $query->getData()->filterBank($params['bank']);
                endif;

                if (null != $params['central']) :
                    $query->getData()->filterCentral($params['central']);
                endif;

                if (null != $params['type']) :
                    $query->getData()->filterTypeTransaction($params['type']);
                endif;

                if (null != $params['isActive']) :
                    $query->getData()->filterIsActive($params['isActive']);
                endif;
            })
            ->paginate($params['items'], $this->moduleDetailField());
        elseif ('trash' == $type) :
            $data = ModelsModuleDefinition::getTrashData()
            ->where(function ($query) use ($params) {
                if (null != $params['groupname']) :
                    $query->getTrashData()->filterGroupName($params['groupname']);
                endif;

                if (null != $params['table']) :
                    $query->getTrashData()->filterTable($params['table']);
                endif;

                if (null != $params['bank']) :
                    $query->getTrashData()->filterBank($params['bank']);
                endif;

                if (null != $params['central']) :
                    $query->getTrashData()->filterCentral($params['central']);
                endif;

                if (null != $params['type']) :
                    $query->getTrashData()->filterTypeTransaction($params['type']);
                endif;

                if (null != $params['isActive']) :
                    $query->getTrashData()->filterIsActive($params['isActive']);
                endif;
            })
            ->paginate($params['items'], $this->moduleDetailField());
        endif;

        // Hitung Jumlah Data
        $count = count($data);

        // Null == False | !Null == Data
        return (null == $count) ? false : $data;
    }
}
