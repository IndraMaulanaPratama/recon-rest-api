<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreProductFunds extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $primaryKey = 'CSC_PF_ID';
    protected $table = 'CSCCORE_PRODUCT_FUNDS';

    protected $fillable = [
        'CSC_PF_ID',
        'CSC_PF_PRODUCT',
        'CSC_PF_GROUP_TRANSFER',
    ];

    public function scopeSearchData($query, $group, $product)
    {
        $query->where([
            'CSC_PF_GROUP_TRANSFER' => $group,
            'CSC_PF_PRODUCT' => $product
        ]);

        return $query;
    }

    public function scopeSearchById($query, $id)
    {
        $query->where([
            'CSC_PF_ID' => $id
        ]);

        return $query;
    }

    public function scopeSearchProduct($query, $product)
    {
        $query->where([
            'CSC_PF_PRODUCT' => $product
        ]);

        return $query;
    }

    public function scopeSearchByAccount($query, $id)
    {
        $query->where('CSC_PF_GROUP_TRANSFER', $id);

        return $query;
    }

    public function scopeCheckExists($query, $product, $group)
    {
        return $query->where('CSC_PF_PRODUCT', $product)
        ->where('CSC_PF_GROUP_TRANSFER', $group);
    }

    public function scopeSimpleData()
    {
        return  [
            'CSC_PF_ID AS ID',
            'CSC_PF_PRODUCT AS PRODUCT',
            'CSC_PF_GROUP_TRANSFER AS GROUP_TRANSFER',
        ];
    }

    public function scopePaginateData()
    {
        return  [
            'CSC_PF_ID AS ID',
            'CSC_PF_PRODUCT AS PRODUCT',
            'CSC_PF_GROUP_TRANSFER AS GROUP_TRANSFER',
        ];
    }
}
