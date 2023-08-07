<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreProfileProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_PROFILE_PRODUCT';
    protected $primaryKey = 'CSC_PP_ID';

    protected $fillable = [
        'CSC_PP_ID',
        'CSC_PP_PROFILE',
        'CSC_PP_PRODUCT',
        'CSC_PP_FORMULA_TRANSFER',
        'CSC_PP_FEE_ADMIN',
        'CSC_PP_FEE_BILLER',
        'CSC_PP_FEE_VSI',
        'CSC_PP_CLAIM_PARTNER',
        'CSC_PP_CLAIM_VSI',
        'CSC_PP_MULTIPLIER_TYPE',
        'CSC_PP_PARTNER_BILLING_TYPE',
        'CSC_PP_BILLER_BILLING_TYPE',
    ];

    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_PP_ID', $id);

        return $query;
    }

    public function scopeSearchByProfile($query, $id)
    {
        $query->where('CSC_PP_PROFILE', $id);

        return $query;
    }

    public function scopeSearchByProduct($query, $id)
    {
        $query->where('CSC_PP_PRODUCT', $id);

        return $query;
    }

    public function scopeCheckExistCopy($query, $profile, $product, $formula)
    {
        return $query->where(
            [
                'CSC_PP_PROFILE' => $profile,
                'CSC_PP_PRODUCT'=> $product,
                'CSC_PP_FORMULA_TRANSFER' => $formula,
            ]
        );
    }

    public function scopeCheckExistProduct($query, $id, $product)
    {
        return $query->where(
            [
                'CSC_PP_ID' => $id,
                'CSC_PP_PRODUCT'=> $product,
            ]
        );
    }


    public function scopeSimpleData($query, $items)
    {
        return [
            'CSC_PP_ID AS ID',
            'CSC_PP_PROFILE AS PROFILE',
            'CSC_PP_PRODUCT AS PRODUCT',
        ];
    }

    public function scopePaginateData($query, $items)
    {
        return [
            'CSC_PP_ID AS ID',
            'CSC_PP_PROFILE AS PROFILE',
            'CSC_PP_PRODUCT AS PRODUCT',
            'CSC_PP_FORMULA_TRANSFER AS FORMULA_TRANSFER',
            'CSC_PP_FEE_ADMIN AS FEE_ADMIN',
            'CSC_PP_FEE_BILLER AS FEE_BILLER',
            'CSC_PP_FEE_VSI AS FEE_VSI',
            'CSC_PP_CLAIM_PARTNER AS CLAIM_PARTNER',
            'CSC_PP_CLAIM_VSI AS CLAIM_VSI',
            'CSC_PP_MULTIPLE_TYPE AS MULTIPLE_TYPE',
            'CSC_PP_BILLER_BILLING_TYPE AS BILLING_TYPE',
        ];
    }
}
