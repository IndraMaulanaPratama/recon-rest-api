<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBillerProduct extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_BILLER_PRODUCT';
    protected $primaryKey = 'CSC_BP_ID';

    protected $fillable = [
        'CSC_BP_ID',
        'CSC_BP_PRODUCT',
        'CSC_BP_BILLER',
    ];


    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_BP_ID', $id);

        return $query;
    }

    public function scopeSearchProduct($query, $id, $biller)
    {
        $query->where(
            [
                'CSC_BP_PRODUCT' => $id,
                'CSC_BP_BILLER' => $biller
            ]
        );
        return $query;
    }

    public function scopeCekProduct($query, $product)
    {
        return $query->where(
            [
                'CSC_BP_PRODUCT' => $product,
            ]
        );
    }

    public function scopePaginateData()
    {
        return [
                    'CSC_BP_ID AS ID',
                    'CSC_BP_PRODUCT AS ID_PRODUCT',
                    'CSC_BP_BILLER AS ID_BILLER',
                ];
    }
}
