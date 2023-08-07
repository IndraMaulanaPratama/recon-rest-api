<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreCalendar extends Model
{
    use HasFactory;
    public $timestamps = false;

    // protected $connection = 'devel_report';
    protected $connection = 'server_report';
    protected $table = 'CSCCORE_CALENDAR';
    protected $primaryKey = 'CSC_CAL_ID';

    protected $fillable = [
        'CSC_CAL_ID',
        'CSC_CAL_NAME',
        'CSC_CAL_DEFAULT',
        'CSC_CAL_CREATED_DT',
        'CSC_CAL_CREATED_BY',
        'CSC_CAL_MODIFIED_DT',
        'CSC_CAL_MODIFIED_BY',
        'CSC_CAL_DELETED_DT',
        'CSC_CAL_DELETED_BY',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_CAL_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_CAL_DELETED_DT')
            ->where('CSC_CAL_ID', $id);

        return $query;
    }

    public function scopeSearchDefault($query)
    {
        $query->whereNull('CSC_CAL_DELETED_DT')
            ->where('CSC_CAL_DEFAULT', 0);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_CAL_DELETED_DT');

        return $query;
    }

    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_CAL_DELETED_DT')
            ->where('CSC_CAL_ID', $id);

        return $query;
    }

    public function scopePaginateData($query, $items)
    {
        return [
            'CSC_CAL_ID AS ID',
            'CSC_CAL_NAME AS NAME',
            'CSC_CAL_DEFAULT AS DEFAULT',
            'CSC_CAL_CREATED_DT AS CREATED',
            'CSC_CAL_MODIFIED_DT AS UPDATED',
            'CSC_CAL_CREATED_BY AS CREATED_AT',
            'CSC_CAL_MODIFIED_BY AS UPDATED_AT',
        ];
    }

    public function scopeSimpleData()
    {
        return  [
            'CSC_CAL_ID AS ID',
            'CSC_CAL_NAME AS NAME'
        ];
    }
}
