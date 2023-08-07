<?php

namespace App\Models;

use Database\Seeders\ClientSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'client';
    protected $table = 'CSCMOD_CLIENT';
    protected $primaryKey = 'csm_c_id';
    protected $connection = 'recon_auth';
    // public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'csm_c_name',
        'csm_c_id',
        'csm_c_secret',
        'csm_c_key',
        'csm_c_bearer'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->csm_c_secret;
    }

    public function personalAccessToken()
    {
        return $this->hasMany(PersonalAccessToken::class, 'name');
    }

    public function run()
    {
        $this->call([
            ClientSeeder::class
        ]);
    }
}
