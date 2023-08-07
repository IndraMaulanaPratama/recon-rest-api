<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreFormulaTransfer extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_FORMULA_TRANSFER';
    protected $primaryKey = 'CSC_FH_ID';

    protected $fillable = [
        'CSC_FH_ID',
        'CSC_FH_FORMULA',
        'CSC_FH_STATUS'
    ];

    public function scopeGetData($query)
    {
        return $query->where('CSC_FH_STATUS', 0);
    }

    public function scopeTrashData($query)
    {
        return $query->where('CSC_FH_STATUS', 1);
    }

    public function scopeSearchData($query, $id)
    {
        return $query->where('CSC_FH_STATUS', 0)
        ->where('CSC_FH_ID', $id);
    }

    public function scopeSearchFormula($query, $formula)
    {
        return $query->where('CSC_FH_STATUS', 0)
        ->where('CSC_FH_FORMULA', 'LIKE', '%'. $formula .'%');
    }

    public function scopeSearchStatus($query, $status)
    {
        return $query->where('CSC_FH_STATUS', 0)
        ->where('CSC_FH_STATUS', $status);
    }

    public function scopeSearchTrashData($query, $id)
    {
        return $query->where('CSC_FH_STATUS', 1)
        ->where('CSC_FH_ID', $id);
    }
}
