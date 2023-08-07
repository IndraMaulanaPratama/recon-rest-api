<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExludePartner extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_EXCLUDE_PARTNER';
    protected $primaryKey = 'CSC_EP_ID';

    protected $fillable = [
        'CSC_EP_ID',
        'CSC_EP_CID',
        'CSC_EP_PRODUCT',
    ];

    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_EP_ID', $id);

        return $query;
    }

    public function scopePaginateData()
    {
        return [
            'CSC_EP_ID AS ID',
            'CSC_EP_CID AS CID',
            'CSC_EP_PRODUCT AS PRODUCT',
        ];
    }

    public function scopeSearchDataByClient($query, $client, $product)
    {
        $query->where(['CSC_EP_CID' => $client, 'CSC_EP_PRODUCT' => $product]);
        return $query;
    }
}
