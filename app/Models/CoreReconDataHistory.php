<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreReconDataHistory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_RECON_DATA_HISTORY';
    protected $primaryKey = 'CSC_RDTH_ID';

    protected $fillable =[
        'CSC_RDTH_RECON_ID',
        'CSC_RDTH_PRODUCT',
        'CSC_RDTH_CID',
        'CSC_RDTH_TRX_DT',
        'CSC_RDTH_SETTLED_DT',
        'CSC_RDTH_NBILL',
        'CSC_RDTH_NMONTH',
        'CSC_RDTH_FEE',
        'CSC_RDTH_FEE_ADMIN',
        'CSC_RDTH_FEE_VSI',
        'CSC_RDTH_BILLER_AMOUNT',
        'CSC_RDTH_USER_SETTLED',
        'CSC_RDTH_STATUS',
        'CSC_RDTH_GENERATED_DT',
        'CSC_RDTH_VERSION',
    ];

    public function scopeRecon($query, $id)
    {
        return $query->where('CSC_RDTH_RECON_ID', $id);
    }

    public function scopeProduct($query, $product)
    {
        return $query->where('CSC_RDTH_PRODUCT', $product);
    }

    public function scopeCid($query, $cid)
    {
        return $query->where('CSC_RDTH_CID', $cid);
    }

    public function scopeDate($query, $date)
    {
        return $query->where('CSC_RDTH_TRX_DT', $date);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('CSC_RDTH_STATUS', $status);
    }

    public function scopeVersion($query, $version)
    {
        return $query->where('CSC_RDTH_VERSION', $version);
    }

    public function scopeDateRange($query, $date)
    {
        return $query->whereBetween('CSC_RDTH_TRX_DT', $date);
    }
}
