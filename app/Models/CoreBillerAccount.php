<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBillerAccount extends Model
{
    use HasFactory;
    public $timestamps = false;

    // protected $connection = 'devel_report';
    protected $connection = 'server_report';

    protected $table = 'CSCCORE_BILLER_ACCOUNT';
    protected $primaryKey = 'CSC_BA_ID';

    protected $fillable = [
        'CSC_BA_ID',
        'CSC_BA_ACCOUNT',
        'CSC_BA_BILLER'
    ];

    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_BA_ID', $id);

        return $query;
    }

    public function scopeSearchAccount($query, $id)
    {
        $query->where('CSC_BA_Account', $id);

        return $query;
    }

    public function scopeCheckAccount($query, $account, $biller)
    {
        $query->where(
            [
                'CSC_BA_ACCOUNT' => $account,
                'CSC_BA_BILLER' => $biller
            ]
        );

        return $query;
    }

    public function scopePaginateData()
    {
        return [
            'CSC_BA_ID AS ID',
            'CSC_BA_ACCOUNT',
            'CSC_BA_BILLER'
        ];
    }
}
