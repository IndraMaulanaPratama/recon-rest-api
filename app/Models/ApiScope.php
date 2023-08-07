<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiScope extends Model
{
    use HasFactory;

    protected $connection = 'recon_auth';
    protected $table = 'CSCMOD_CLIENT_API_SCOPE';
    protected $primaryKey = 'CSM_CAS_ID';
    public $timestamps = false;

    protected $fillable = [
        'CSM_CAS_ID', 'CSM_CAS_ClIENT', 'CSM_CAS_API_NAME'
    ];

    public function scopeSearchData($query, $id)
    {
        $query->where('CSM_CAS_ID', $id);

        return $query;
    }

    public function scopeSearchDataClient($query, $id)
    {
        $query->where('CSM_CAS_CLIENT', $id);

        return $query;
    }

    public function scopeSearchDataByClient($query, $client, $api)
    {
        $query->where(['CSM_CAS_CLIENT' => $client, 'CSM_CAS_API_NAME' => $api]);
        return $query;
    }
}
