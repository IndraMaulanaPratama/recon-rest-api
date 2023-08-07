<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReconDana extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_RECON_DANA';
    protected $primaryKey = 'CSC_RDN_ID';

    protected $fillable = [
        'CSC_RDN_ID',
        'CSC_RDN_BILLER',
        'CSC_RDN_GROUP_TRANSFER',
        'CSC_RDN_START_DT',
        'CSC_RDN_END_DT',
        'CSC_RDN_SETTLED_DT',
        'CSC_RDN_DESC_TRANSFER',
        'CSC_RDN_SUSPECT_PROCESS',
        'CSC_RDN_SUSPECT_PROCESS_VALUE',
        'CSC_RDN_SUSPECT_UNPROCESS',
        'CSC_RDN_SUSPECT_UNPROCESS_VALUE',
        'CSC_RDN_CORRECTION_PROCESS',
        'CSC_RDN_CORRECTION_PROCESS_VALUE',
        'CSC_RDN_CORRECTION_UNPROCESS',
        'CSC_RDN_CORRECTION_UNPROCESS_VALUE',
        'CSC_RDN_AMOUNT',
        'CSC_RDN_AMOUNT_TRANSFER',
        'CSC_RDN_REAL_TRANSFER',
        'CSC_RDN_DIFF_TRANSFER',
        'CSC_RDN_TYPE',
        'CSC_RDN_USER_PROCESS',
        'CSC_RDN_STATUS',
    ];

    // Search Dana By Id
    public function scopeId($query, $id)
    {
        return $query->where('CSC_RDN_ID', $id);
    }

    // Search Dana By Id
    public function scopeType($query, $type)
    {
        return $query->where('CSC_RDN_TYPE', $type);
    }

    // Search Dana By Id
    public function scopeStatus($query, $status)
    {
        return $query->where('CSC_RDN_STATUS', $status);
    }

    public function scopeGroupTransfer($query, $id)
    {
        return $query->where('CSC_RDN_GROUP_TRANSFER', $id);
    }

    // Search Dana By Settled Date
    public function scopeSettledDt($query, $date)
    {
        return $query->where('CSC_RDN_SETTLED_DT', $date);
    }

    // Search date range by Start End Date
    public function scopeStartEndDate($query, $date)
    {
        return $query->whereBetween('CSC_RDN_SETTLED_DT', $date);
    }

    // Get List Summary
    public function scopeListSummary($query)
    {
        return $query->join(
            'CSCCORE_BILLER AS B',
            'CSC_RDN_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNull('B.CSC_BILLER_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_COLLECTION AS BC',
            'B.CSC_BILLER_ID',
            '=',
            'BC.CSC_BC_BILLER'
        )
        ->join(
            'CSCCORE_GROUP_BILLER AS GB',
            'BC.CSC_BC_GROUP_BILLER',
            '=',
            'GB.CSC_GB_ID'
        )
        ->whereNull('GB.CSC_GB_DELETED_DT');
    }

    // Get Process/Unprocess Summary
    public function scopeProcessSummary($query)
    {
        return $query->select(
            DB::raw('SUM(CSC_RDN_AMOUNT_TRANSFER) AS AMOUNT')
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'CSC_RDN_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->whereNull('TD.CSC_TD_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'TD.CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNull('B.CSC_BILLER_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_COLLECTION AS BC',
            'B.CSC_BILLER_ID',
            '=',
            'BC.CSC_BC_BILLER'
        )
        ->join(
            'CSCCORE_GROUP_BILLER AS GB',
            'BC.CSC_BC_GROUP_BILLER',
            '=',
            'GB.CSC_GB_ID'
        )
        ->whereNull('GB.CSC_GB_DELETED_DT')
        ->groupBy('CSC_RDN_SETTLED_DT');
    }

    // Get List Summary for List Recon
    public function scopeSummary($query)
    {
        return $query->join(
            'CSCCORE_BILLER AS BILLER',
            'CSC_RDN_BILLER',
            '=',
            'BILLER.CSC_BILLER_ID'
        )
        ->whereNull('BILLER.CSC_BILLER_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_COLLECTION AS BC',
            'BILLER.CSC_BILLER_ID',
            '=',
            'BC.CSC_BC_BILLER'
        )
        ->join(
            'CSCCORE_GROUP_BILLER AS GB',
            'BC.CSC_BC_GROUP_BILLER',
            '=',
            'GB.CSC_GB_ID'
        )
        ->whereNull('GB.CSC_GB_DELETED_DT')
        ->join(
            'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
            'CSC_RDN_GROUP_TRANSFER',
            '=',
            'GTF.CSC_GTF_ID'
        )
        ->whereNull('GTF.CSC_GTF_DELETED_DT')
        ->join(
            'CSCCORE_ACCOUNT AS ACCOUNT',
            'GTF.CSC_GTF_SOURCE',
            '=',
            'ACCOUNT.CSC_ACCOUNT_NUMBER'
        )
        ->whereNull('ACCOUNT.CSC_ACCOUNT_DELETED_DT')
        ->join(
            'CSCCORE_BANK AS BANK',
            'ACCOUNT.CSC_ACCOUNT_BANK',
            '=',
            'BANK.CSC_BANK_CODE'
        )
        ->whereNull('BANK.CSC_BANK_DELETED_DT');
    }

    // Join To Biller
    public function scopeJoinBiller($query)
    {
        return $query->join(
            'CSCCORE_BILLER AS BILLER',
            'CSC_RDN_BILLER',
            '=',
            'BILLER.CSC_BILLER_ID'
        );
    }

    // Join To Group Transfer
    public function scopeJoinGroupTransfer($query)
    {
        return $query->join(
            'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
            'CSC_RDN_GROUP_TRANSFER',
            '=',
            'GTF.CSC_GTF_ID'
        );
    }
}
