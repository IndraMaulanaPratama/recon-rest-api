<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorePartner extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_PARTNER';
    protected $primaryKey = 'CSC_PARTNER_ID';

    protected $fillable = [
        'CSC_PARTNER_ID',
        'CSC_PARTNER_NAME',
        'CSC_PARTNER_CREATED_BY',
        'CSC_PARTNER_CREATED_DT',
        'CSC_PARTNER_MODIFIED_BY',
        'CSC_PARTNER_MODIFIED_DT',
        'CSC_PARTNER_DELETED_BY',
        'CSC_PARTNER_DELETED_DT',

    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_PARTNER_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_PARTNER_DELETED_DT')
            ->where('CSC_PARTNER_ID', $id);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_PARTNER_DELETED_DT');

        return $query;
    }

    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_PARTNER_DELETED_DT')
            ->where('CSC_PARTNER_ID', $id);

        return $query;
    }

    public function scopePaginateData($query, $items)
    {
        return [
            'CSC_PARTNER_ID AS ID',
            'CSC_PARTNER_NAME AS NAME',
            'CSC_PARTNER_CREATED_DT AS CREATED',
            'CSC_PARTNER_MODIFIED_DT AS UPDATED',
            'CSC_PARTNER_CREATED_BY AS CREATED_BY',
            'CSC_PARTNER_MODIFIED_BY AS UPDATED_BY',
        ];
    }

    public function scopeSimpleData()
    {
        return  [
            'CSC_PARTNER_ID AS ID',
            'CSC_PARTNER_NAME AS NAME'
        ];
    }

    public function scopeSingleData($query)
    {
        return $query->first([
            'CSC_PARTNER_ID AS ID',
            'CSC_PARTNER_NAME AS NAME'
        ]);
    }
}
