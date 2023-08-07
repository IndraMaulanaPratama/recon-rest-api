<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreReconData extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_RECON_DATA';
    protected $primaryKey = 'CSC_RDT_ID';

    protected $fillable = [
        'CSC_RDT_ID',
        'CSC_RDT_RECON_DANA_ID',
        'CSC_RDT_PRODUCT',
        'CSC_RDT_CID',
        'CSC_RDT_TRX_DT',
        'CSC_RDT_SETTLED_DT',
        'CSC_RDT_NBILL',
        'CSC_RDT_NMONTH',
        'CSC_RDT_FEE',
        'CSC_RDT_FEE_ADMIN',
        'CSC_RDT_FEE_VSI',
        'CSC_RDT_BILLER_AMOUNT',
        'CSC_RDT_USER_SETTLED',
        'CSC_RDT_STATUS',
    ];

    // Cari By Id
    public function scopeSearchData($query, $id)
    {
        return $query->where('CSC_RDT_ID', $id);
    }

    public function scopeReconDana($query, $id)
    {
        return $query->where('CSC_RDT_RECON_DANA_ID', $id);
    }

    // Cari By Product
    public function scopeProduct($query, $product)
    {
        return $query->where('CSC_RDT_PRODUCT', $product);
    }

    // Cari By CID
    public function scopeCid($query, $cid)
    {
        return $query->where('CSC_RDT_CID', $cid);
    }

    // Cari By Tanggal
    public function scopeDate($query, $date)
    {
        return $query->where('CSC_RDT_TRX_DT', $date);
    }


    // Cari By Range Tanggal
    public function scopeDateRange($query, $date)
    {
        return $query->whereBetween('CSC_RDT_TRX_DT', $date);
    }

    // Search By Status
    public function scopeStatus($query, $status)
    {
        return $query->where('CSC_RDT_STATUS', $status);
    }

    // Join Table Formula Transfer
    public function scopeJoinFormulaTransfer($query)
    {
        return $query->join(
            'CSCCORE_FORMULA_TRANSFER AS FH',
            'FH.CSC_FH_ID',
            'CSC_RDT_FORMULA_TRANSFER'
        );
    }
}
