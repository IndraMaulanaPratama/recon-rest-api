<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreGroupTransferFunds extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $primaryKey = 'CSC_GTF_ID';
    protected $table = 'CSCCORE_GROUP_TRANSFER_FUNDS';

    protected $fillable = [
        'CSC_GTF_ID',
        'CSC_GTF_SOURCE',
        'CSC_GTF_DESTINATION',
        'CSC_GTF_NAME',
        'CSC_GTF_TRANSFER_TYPE',
        'CSC_GTF_TRANSFER_DESC',
        'CSC_GTF_PRODUCT_COUNT',
        'CSC_GTF_CREATED_BY',
        'CSC_GTF_CREATED_DT',
        'CSC_GTF_MODIFIED_BY',
        'CSC_GTF_MODIFIED_DT',
        'CSC_GTF_DELETED_BY',
        'CSC_GTF_DELETED_DT',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_GTF_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_GTF_DELETED_DT')
            ->where('CSC_GTF_ID', $id);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_GTF_DELETED_DT');

        return $query;
    }

    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_GTF_DELETED_DT')
            ->where('CSC_GTF_ID', $id);

        return $query;
    }

    public function scopeSimpleData()
    {
        return  [
            'CSC_GTF_ID AS ID',
            'CSC_GTF_NAME AS NAME',
            'CSC_GTF_SOURCE AS ACCOUNT_SRC',
            'CSC_GTF_DESTINATION AS ACCOUNT_DEST',
            'CSC_GTF_TRANSFER_TYPE AS TYPE',
            'CSC_GTF_PRODUCT_COUNT AS PRODUCT_COUNT',
        ];
    }

    public function scopePaginateData()
    {
        return [
            'CSC_GTF_ID AS ID',
            'CSC_GTF_NAME AS NAME',
            'CSC_GTF_SOURCE AS ACCOUNT_SRC',
            'CSC_GTF_DESTINATION AS ACCOUNT_DEST',
            'CSC_GTF_PRODUCT_COUNT AS PRODUCT_COOUNT',
        ];
    }
}
