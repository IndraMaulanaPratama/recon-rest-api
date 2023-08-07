<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreGroupOfProduct extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_GROUP_OF_PRODUCT';
    protected $primaryKey = 'CSC_GOP_PRODUCT_NAME';

    protected $fillable = [
        'CSC_GOP_PRODUCT_NAME',
        'CSC_GOP_PRODUCT_GROUP',
        'CSC_GOP_PRODUCT_PARENT_PRODUCT',
    ];

    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_GOP_PRODUCT_NAME', $id);

        return $query;
    }

    public function scopeModul($query, $id)
    {
        $query->where('CSC_GOP_PRODUCT_PARENT_PRODUCT', $id);

        return $query;
    }

    public function scopeSearchBiller($query, $id)
    {
        $query->where('CSC_GOP_PRODUCT_GROUP', $id);

        return $query;
    }

    public function scopeSearchTrashData($query, $id)
    {
        $query->where('CSC_GOP_PRODUCT_NAME', $id);

        return $query;
    }


    public function scopePaginateData()
    {
        return [
            'CSC_GOP_PRODUCT_NAME',
            'CSC_GOP_PRODUCT_GROUP',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT',
        ];
    }
}
