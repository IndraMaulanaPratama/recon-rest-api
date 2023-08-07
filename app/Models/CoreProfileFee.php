<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreProfileFee extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_PROFILE_FEE';
    protected $primaryKey = 'CSC_PROFILE_ID';

    protected $fillable = [
        'CSC_PROFILE_ID',
        'CSC_PROFILE_NAME',
        'CSC_PROFILE_DESC',
        'CSC_PROFILE_DEFAULT',
        'CSC_PROFILE_CREATED_BY',
        'CSC_PROFILE_CREATED_DT',
        'CSC_PROFILE_MODIFIED_BY',
        'CSC_PROFILE_MODIFIED_DT',
        'CSC_PROFILE_DELETED_BY',
        'CSC_PROFILE_DELETED_DT',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_PROFILE_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_PROFILE_DELETED_DT')
            ->where('CSC_PROFILE_ID', $id);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_PROFILE_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_PROFILE_DELETED_DT')
            ->where('CSC_PROFILE_ID', $id);

        return $query;
    }

    public function scopeSimpleData($query, $items)
    {
        return [
            'CSC_PROFILE_ID AS PROFILE_ID',
            'CSC_PROFILE_NAME AS PROFILE_NAME',
            'CSC_PROFILE_DEFAULT AS PROFILE_DESC',
            'CSC_PROFILE_CREATED_DT AS CREATED',
            'CSC_PROFILE_MODIFIED_DT AS MODIFIED',
            'CSC_PROFILE_CREATED_BY AS  CREATED_BY',
            'CSC_PROFILE_MODIFIED_BY AS MODIFIED_BY',
        ];
    }


    public function scopePaginateData($query, $items)
    {
        return [
            'CSC_PROFILE_ID AS PROFILE_ID',
            'CSC_PROFILE_NAME AS PROFILE_NAME',
            'CSC_PROFILE_DEFAULT AS PROFILE_DESC',
            'CSC_PROFILE_CREATED_DT AS CREATED',
            'CSC_PROFILE_MODIFIED_DT AS MODIFIED',
            'CSC_PROFILE_CREATED_BY AS  CREATED_BY',
            'CSC_PROFILE_MODIFIED_BY AS MODIFIED_BY',
        ];
    }
}
