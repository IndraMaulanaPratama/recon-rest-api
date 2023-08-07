<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBiller extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_BILLER';
    protected $primaryKey = 'CSC_BILLER_ID';

    protected $fillable = [
        'CSC_BILLER_ID',
        'CSC_BILLER_GROUP_PRODUCT',
        'CSC_BILLER_PROFILE',
        'CSC_BILLER_NAME',
        'CSC_BILLER_CREATED_BY',
        'CSC_BILLER_CREATED_DT',
        'CSC_BILLER_MODIFIED_BY',
        'CSC_BILLER_MODIFIED_DT',
        'CSC_BILLER_DELETED_BY',
        'CSC_BILLER_DELETED_DT',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_BILLER_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_BILLER_DELETED_DT')
            ->where('CSC_BILLER_ID', $id);

        return $query;
    }

    public function scopeGroupBiller($query, $id)
    {
        $query->whereNull('CSC_BILLER_DELETED_DT')
            ->where('CSC_BILLER_GROUP_PRODUCT', $id);

        return $query;
    }

    public function scopeJoinProfileFee($query)
    {
        return $query->join(
            'CSCCORE_PROFILE_FEE AS PF',
            'CSC_BILLER_PROFILE',
            '=',
            'PF.CSC_PROFILE_ID'
        );
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_BILLER_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_BILLER_DELETED_DT')
            ->where('CSC_BILLER_ID', $id);

        return $query;
    }


    public function scopePaginateData($query, $items)
    {
        $query->paginate(
            $perpage = $items,
            $column = [
                'CSC_BILLER_ID AS ID',
                'CSC_BILLER_NAME AS NAME',
                'CSC_BILLER_CREATED_DT AS CREATED',
                'CSC_BILLER_MODIFIED_DT AS MODIFIED',
                'CSC_BILLER_CREATED_BY AS  CREATED_BY',
                'CSC_BILLER_MODIFIED_BY AS MODIFIED_BY',
            ]
        );

        return $query;
    }
}
