<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreCorrection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_CORRECTION';
    protected $primaryKey = 'CSC_CORR_ID';


    protected $fillable = [
        'CSC_CORR_ID',
        'CSC_CORR_GROUP_TRANSFER',
        'CSC_CORR_DATE',
        'CSC_CORR_RECON_DANA_ID',
        'CSC_CORR_DATE_TRANSFER',
        'CSC_CORR_CORRECTION',
        'CSC_CORR_CORRECTION_VALUE',
        'CSC_CORR_AMOUNT_TRANSFER',
        'CSC_CORR_DATE_PINBUK',
        'CSC_CORR_DESC',
        'CSC_CORR_CREATED_BY',
        'CSC_CORR_CREATED_DT',
        'CSC_CORR_MODIFIED_BY',
        'CSC_CORR_MODIFIED_DT',
        'CSC_CORR_DELETED_BY',
        'CSC_CORR_DELETED_DT',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_CORR_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_CORR_DELETED_DT')
            ->where('CSC_CORR_ID', $id);

        return $query;
    }

    public function scopeReconDana($query, $id)
    {
        return $query->where('CSC_CORR_RECON_DANA_ID', $id);
    }

    public function scopeDate($query, $date)
    {
        return $query->where('CSC_CORR_DATE', $date);
    }

    public function scopeDateRange($query, $date)
    {
        return $query->whereBetween('CSC_CORR_DATE', $date);
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_CORR_DELETED_DT');

        return $query;
    }

    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_CORR_DELETED_DT')
            ->where('CSC_CORR_ID', $id);

        return $query;
    }

    public function scopeSimpleData()
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

    public function scopePaginateData()
    {
        return [
            'CSC_CORR_ID AS ID',
            'CSC_CORR_GROUP_TRANSFER AS GROUP_TRANSFER',
            'CSC_CORR_DATE AS DATE',
            'CSC_CORR_DATE_TRANSFER AS DATE_TRANSFER',
            'CSC_CORR_CORRECTION AS CORRECTION',
            'CSC_CORR_CORRECTION_VALUE AS CORRECTION_VALUE',
            'CSC_CORR_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
            'CSC_CORR_DATE_PINBUK AS DATE_PINBUK',
            'CSC_CORR_DESC AS DESC',
            'CSC_CORR_CREATED_DT AS CREATED',
            'CSC_CORR_MODIFIED_DT AS MODIFIED',
            'CSC_CORR_CREATED_BY AS CREATED_BY',
            'CSC_CORR_MODIFIED_BY AS MODIFIED_BY',
            'CSC_CORR_DELETED_BY AS DELETED_BY',
            'CSC_CORR_DELETED_DT AS DELETED',
        ];
    }
}
