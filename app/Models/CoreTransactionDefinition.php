<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreTransactionDefinition extends Model
{
    use HasFactory;

    // const DELETED_AT = 'CSC_TD_DELETED_AT';

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_TRANSACTION_DEFINITION';
    public $timestamps = false;
    protected $primaryKey = 'CSC_TD_NAME';


    protected $fillable = [
        'CSC_TD_NAME',
        'CSC_TD_GROUPNAME',
        'CSC_TD_ALIASNAME',
        'CSC_TD_DESC',
        'CSC_TD_TABLE',
        'CSC_TD_CRITERIA',
        'CSC_TD_FINDCRITERIA',
        'CSC_TD_BANK_CRITERIA',
        'CSC_TD_CENTRAL_CRITERIA',
        'CSC_TD_BANK_COLUMN',
        'CSC_TD_CENTRAL_COLUMN',
        'CSC_TD_TERMINAL_COLUMN',
        'CSC_TD_SUBID_COLUMN',
        'CSC_TD_SUBNAME_COLUMN',
        'CSC_TD_SWITCH_REFNUM_COLUMN',
        'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN',
        'CSC_TD_DATE_COLUMN',
        'CSC_TD_NREK_COLUMN',
        'CSC_TD_NBILL_COLUMN',
        'CSC_TD_BILL_AMOUNT_COLUMN',
        'CSC_TD_ADM_AMOUNT_COLUMN',
        'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0',
        'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1',
        'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2',
        'CSC_TD_TABLE_ARCH',
        'CSC_TD_BANK_GROUPBY',
        'CSC_TD_CENTRAL_GROUPBY',
        'CSC_TD_TERMINAL_GROUPBY',
        'CSC_TD_TYPE_TRX',
        'CSC_TD_ISACTIVE',
        'CSC_TD_CREATED_DT',
        'CSC_TD_MODIFIED_DT',
        'CSC_TD_CREATED_BY',
        'CSC_TD_MODIFIED_BY',
        'CSC_TD_DELETED_DT',
        'CSC_TD_DELETED_BY',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_TD_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_NAME', $id);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_TD_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_NAME', $id);

        return $query;
    }

    public function scopeSimpleData()
    {
        return  [
            'CSC_TD_NAME AS NAME',
            'CSC_TD_GROUPNAME AS GROUP_NAME',
            'CSC_TD_ALIASNAME AS ALIAS_NAME',
            'CSC_TD_DESC AS DESCRIPTION',
        ];
    }

    public function scopePaginateData()
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
            'CSC_TD_ISACTIVE AS ISACTIVE'
        ];
    }
}
