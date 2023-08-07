<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreDownCentral extends Model
{
    use HasFactory;

    // protected $connection = 'devel_recon';
    protected $connection = 'server_recon';
    protected $table = 'CSCCORE_DOWN_CENTRAL';
    protected $primaryKey = 'CSC_DC_ID';
    public $timestamps = false;

    protected $fillable = [
        'CSC_DC_ID',
        'CSC_DC_PROFILE',
        'CSC_DC_NAME',
        'CSC_DC_ADDRESS',
        'CSC_DC_PHONE',
        'CSC_DC_PIC_NAME',
        'CSC_DC_PIC_PHONE',
        'CSC_DC_TYPE',
        'CSC_DC_FUND_TYPE',
        'CSC_DC_TERMINAL_TYPE',
        'CSC_DC_REGISTERED',
        'CSC_DC_ISBLOCKED',
        'CSC_DC_MINIMAL_DEPOSIT',
        'CSC_DC_SHORT_ID',
        'CSC_DC_COUNTER_CODE',
        'CSC_DC_A_ID',
        'CSC_DC_ALIAS_NAME',
        'CSC_DC_CREATED_DT',
        'CSC_DC_CREATED_BY',
        'CSC_DC_MODIFIED_DT',
        'CSC_DC_MODIFIED_BY',
        'CSC_DC_DELETED_DT',
        'CSC_DC_DELETED_BY',
    ];

    public function scopeGetData($query)
    {
        $query->whereNull('CSC_DC_DELETED_DT');

        return $query;
    }

    public function scopeSearchData($query, $id)
    {
        $query->whereNull('CSC_DC_DELETED_DT')
            ->where('CSC_DC_ID', $id);

        return $query;
    }

    public function scopeSearchProfile($query, $id)
    {
        $query->whereNull('CSC_DC_DELETED_DT')
            ->where('CSC_DC_PROFILE', $id);

        return $query;
    }

    public function scopeGetTrashData($query)
    {
        $query->whereNotNull('CSC_DC_DELETED_DT');

        return $query;
    }


    public function scopeSearchTrashData($query, $id)
    {
        $query->whereNotNull('CSC_DC_DELETED_DT')
            ->where('CSC_DC_ID', $id);

        return $query;
    }
}
