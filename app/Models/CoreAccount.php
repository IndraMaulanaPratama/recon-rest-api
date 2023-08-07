<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreAccount extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_ACCOUNT';
    protected $primaryKey = 'CSC_ACCOUNT_NUMBER';

    protected $fillable = [
        'CSC_ACCOUNT_NUMBER',
        'CSC_ACCOUNT_BANK',
        'CSC_ACCOUNT_NAME',
        'CSC_ACCOUNT_OWNER',
        'CSC_ACCOUNT_TYPE',
        'CSC_ACCOUNT_CREATED_BY',
        'CSC_ACCOUNT_CREATED_DT',
        'CSC_ACCOUNT_MODIFIED_BY',
        'CSC_ACCOUNT_MODIFIED_DT',
        'CSC_ACCOUNT_DELETED_BY',
        'CSC_ACCOUNT_DELETED_DT',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_ACCOUNT_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_ACCOUNT_DELETED_DT')
            ->where('CSC_ACCOUNT_NUMBER', $id);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_ACCOUNT_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_ACCOUNT_DELETED_DT')
            ->where('CSC_ACCOUNT_NUMBER', $id);

        return $query;
    }

    public function scopePaginateData($query, $items)
    {
        $query->paginate(
            $perpage = $items,
            $column = [
                'CSC_ACCOUNT_NUMBER AS NUMBER',
                'CSC_ACCOUNT_BANK AS BANK',
                'CSC_ACCOUNT_NAME AS NAME',
                'CSC_ACCOUNT_OWNER AS OWNER',
                'CSC_ACCOUNT_TYPE AS TYPE',
            ]
        );

        return $query;
    }
}
