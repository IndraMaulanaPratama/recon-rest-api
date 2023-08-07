<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDefinitionV2 extends Model
{
    use HasFactory;

    protected $table = 'CSCCORE_TRANSACTION_DEFINITION_V2';
    protected $connection = 'server_report';
    protected $primaryKey = 'CSC_TD_NAME';
    public $timestamps = false;

    protected $fillable = [
        'CSC_TD_NAME',
        'CSC_TD_BILLER_ID',
        'CSC_TD_GROUPNAME',
        'CSC_TD_ALIASNAME',
        'CSC_TD_DESC',
        'CSC_TD_FINDCRITERIA',
        'CSC_TD_PAN',
        'CSC_TD_CREATED_DT',
        'CSC_TD_CREATED_BY',
        'CSC_TD_MODIFIED_DT',
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

    public function scopeBiller($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_BILLER_ID', $id);

        return $query;
    }

    public function scopeGroupName($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_GROUPNAME', $id);

        return $query;
    }

    public function scopeAlias($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_ALIASNAME', $id);

        return $query;
    }

    public function scopeFindCriteria($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_FINDCRITERIA', $id);

        return $query;
    }

    public function scopePan($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_PAN', $id);

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

    public function scopeFilterSearchData($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_NAME', 'LIKE', '%'. $id .'%');

        return $query;
    }

    public function scopeFilterBiller($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_BILLER_ID', 'LIKE', '%'. $id .'%');

        return $query;
    }

    public function scopeFilterGroupName($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_GROUPNAME', 'LIKE', '%'. $id .'%');

        return $query;
    }

    public function scopeFilterPan($query, $id)
    {
        $query->whereNull('CSC_TD_DELETED_DT')
            ->where('CSC_TD_PAN', 'LIKE', '%'. $id .'%');

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
