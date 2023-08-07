<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreGroupBiller extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_GROUP_BILLER';
    protected $primaryKey = 'CSC_GB_ID';

    protected $fillable = [
        'CSC_GB_ID',
        'CSC_GB_NAME',
        'CSC_GB_CREATED_BY',
        'CSC_GB_CREATED_DT',
        'CSC_GB_MODIFIED_BY',
        'CSC_GB_MODIFIED_DT',
        'CSC_GB_DELETED_BY',
        'CSC_GB_DELETED_DT',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_GB_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_GB_DELETED_DT')
            ->where('CSC_GB_ID', $id);

        return $query;
    }

    public function scopeCheckData($query, $id, $name)
    {
        $query->whereNull('CSC_GB_DELETED_DT')
            ->where(
                [
                    'CSC_GB_ID' => $id,
                    'CSC_GB_NAME' => $name,
                ]
            );

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_GB_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_GB_DELETED_DT')
            ->where('CSC_GB_ID', $id);

        return $query;
    }

    public function scopePaginateData($query, $items)
    {
        $query->paginate(
            $perpage = $items,
            $column = [
                'CSC_GB_ID AS ID',
                'CSC_GB_NAME AS NAME',
                'CSC_GB_CREATED_DT AS CREATED',
                'CSC_GB_MODIFIED_DT AS MODIFIED',
                'CSC_GB_CREATED_BY AS  CREATED_BY',
                'CSC_GB_MODIFIED_BY AS MODIFIED_BY',
            ]
        );

        return $query;
    }
}
