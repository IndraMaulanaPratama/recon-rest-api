<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorePartnerCid extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_PARTNER_CID';
    protected $primaryKey = 'CSC_PC_ID';

    protected $fillable = [
        'CSC_PC_ID',
        'CSC_PC_CID',
        'CSC_PC_PARTNER'
    ];

    public function scopeSearchData($query, $id)
    {
        return $query->where('CSC_PC_ID', $id);
    }

    public function scopeSearchByCid($query, $id)
    {
        return $query->where('CSC_PC_CID', $id);
    }

    public function scopeSearchByPartner($query, $id)
    {
        return $query->where('CSC_PC_PARTNER', $id);
    }

    public function scopeCheckData($query, $partner, $cid)
    {
        return $query->where(
            [
                'CSC_PC_PARTNER' => $partner,
                'CSC_PC_CID' => $cid
            ]
        );
    }
}
