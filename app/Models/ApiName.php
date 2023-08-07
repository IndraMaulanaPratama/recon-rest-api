<?php

namespace App\Models;

use Database\Seeders\ApiNameSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiName extends Model
{
    use HasFactory;

    protected $connection = 'recon_auth';
    protected $table = 'CSCMOD_API_NAME';
    protected $primaryKey = 'CSM_AN_ID';
    public $timestamps = false;

    protected $fillable = [
        'CSM_AN_ID', 'CSM_AN_DESC', 'CSM_AN_CREATED_DT', 'CSM_AN_DELETED_DT'
    ];

    public function run()
    {
        $this->call([
            ApiNameSeeder::class
        ]);
    }
}
