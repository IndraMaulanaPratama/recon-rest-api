<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxCorrection extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_TRX_CORRECTION';
    protected $primaryKey = 'CSM_TC_ID';

    protected $fillable = [
        'CSM_TC_ID',
        'CSM_TC_RECON_ID',
        'CSM_TC_PRODUCT',
        'CSM_TC_CID',
        'CSM_TC_SUBID',
        'CSM_TC_TRX_DT',
        'CSM_TC_PROCESS_DT',
        'CSM_TC_NBILL',
        'CSM_TC_NMONTH',
        'CSM_TC_FEE',
        'CSM_TC_FEE_ADMIN',
        'CSM_TC_FEE_VSI',
        'CSM_TC_BILLER_AMOUNT',
        'CSM_TC_SW_REFNUM',
        'CSM_TC_STATUS_TRX',
        'CSM_TC_STATUS_PROCESS',
        'CSM_TC_STATUS_FUNDS',
    ];

    public function scopeSearchData($query, $id)
    {
        return $query->where('CSM_TC_ID', $id);
    }

    public function scopeRecon($query, $recon)
    {
        return $query->where('CSM_TC_RECON_ID', $recon);
    }

    public function scopeReconDana($query, $recon)
    {
        return $query->where('CSM_TC_RECON_DANA_ID', $recon);
    }

    public function scopeNullReconData($query)
    {
        return $query->whereNull('CSM_TC_RECON_ID');
    }

    public function scopeCid($query, $cid)
    {
        return $query->where('CSM_TC_CID', $cid);
    }

    public function scopeDate($query, $date)
    {
        return $query->where('CSM_TC_TRX_DT', $date);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('CSM_TC_STATUS_TRX', $status);
    }

    public function scopeStatusData($query, $status)
    {
        return $query->where('CSM_TC_STATUS_DATA', $status);
    }

    public function scopeStatusFunds($query, $status)
    {
        return $query->where('CSM_TC_STATUS_FUNDS', $status);
    }

    public function scopeProduct($query, $product)
    {
        return $query->where('CSM_TC_PRODUCT', $product);
    }

    public function scopeDateRange($query, $date)
    {
        return $query->whereBetween('CSM_TC_TRX_DT', $date);
    }

    public function scopeReconData($query, $id)
    {
        return $query->where('CSM_TC_RECON_ID', $id);
    }

    public function scopeJoinFormulaTransfer($query)
    {
        return $query->join(
            'CSCCORE_FORMULA_TRANSFER AS FH',
            'FH.CSC_FH_ID',
            '=',
            'CSM_TC_FORMULA_TRANSFER'
        );
    }
}
