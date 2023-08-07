<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBank extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_BANK';
    protected $primaryKey = 'CSC_BANK_CODE';

    protected $fillable = [
        'CSC_BANK_CODE',
        'CSC_BANK_NAME',
        'CSC_BANK_CREATED_BY',
        'CSC_BANK_CREATED_DT',
        'CSC_BANK_MODIFIED_BY',
        'CSC_BANK_MODIFIED_DT',
        'CSC_BANK_DELETED_BY',
        'CSC_BANK_DELETED_DT',
    ];


    public function scopeGetData($query)
    {
        $query->whereNull('CSC_BANK_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        return $query->whereNull('CSC_BANK_DELETED_DT')
            ->where('CSC_BANK_CODE', $id);
    }

    public function scopename($query, $name)
    {
        $query->whereNull('CSC_BANK_DELETED_DT')
        ->where('CSC_BANK_NAME', $name);

        return $query;
    }


    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_BANK_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_BANK_DELETED_DT')
            ->where('CSC_BANK_CODE', $id);

        return $query;
    }

    public function scopePaginateData($query, $items)
    {
        $query->paginate(
            $perpage = $items,
            $column = [
                'CSC_BANK_CODE AS CODE',
                'CSC_BANK_NAME AS NAME',
            ]
        );

        return $query;
    }
}
