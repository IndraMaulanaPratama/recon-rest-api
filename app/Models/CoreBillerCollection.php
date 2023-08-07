<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBillerCollection extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_BILLER_COLLECTION';
    protected $primaryKey = 'CSC_BC_ID';

    protected $fillable = [
        'CSC_BC_ID',
        'CSC_BC_GROUP_BILLER',
        'CSC_BC_BILLER'
    ];

    public function scopeSearchData($query, $id)
    {
        return $query->where('CSC_BC_ID', $id);
    }

    public function scopeSearchByBiller($query, $id)
    {
        return $query->where('CSC_BC_BILLER', $id);
    }

    public function scopeSearchByGroupBiller($query, $id)
    {
        return $query->where('CSC_BC_GROUP_BILLER', $id);
    }

    public function scopeSearchByGroupAndBiller($query, $group, $biller)
    {
        return $query->where(
            [
                'CSC_BC_GROUP_BILLER' => $group,
                'CSC_BC_BILLER' => $biller
            ]
        );
    }
}
