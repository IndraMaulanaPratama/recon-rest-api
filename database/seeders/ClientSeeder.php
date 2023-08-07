<?php

namespace Database\Seeders;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::insert([
            [
                'csm_c_id' => 'superadminAPI',
                'csm_c_secret' => '$2y$10$v4G4KBRzGmHnAXUDASBoXeSYkKUYsWoANXxwXt3arD.vKEC3hkX8C',
                'csm_c_name' => 'Super Admin',
                'csm_c_status' => '0',
                'csm_c_key' => '2d398821-e738-4e77-b766-67e705aa6ae9',
                'csm_c_bearer' => '1|zlsttvwZE1JX1rmkfrtPUmQmUSkyaWvNPBd21mwRQGLkJgrU4DjXlAIW1m7j',
                'remember_token' => '3oXKtHllsbOBsDIReRy3pOveDLMqkOFm3edr9Mpe6xBiA9tSvj6DlX4eGUBl',
                'created_at' => Carbon::now(('Asia/Jakarta')),
                'updated_at' => null,
            ],

            [
                'csm_c_id' => 'autorecon',
                'csm_c_secret' => '$2y$10$WDAj/Xpu1LtRx3vZicQNzuJEBQgLSkFSaevGP9vlE8w5y1/c8tpkS',
                'csm_c_name' => 'Auto Recon',
                'csm_c_status' => 0,
                'csm_c_key' => 'bdbbc9c0-574a-4bfa-aeaf-7bdb9b407b0c',
                'csm_c_bearer' => '2|2TFXz3WgcT305sAkbExLbCQeENiwLhqk0yJsYvCcTrUk9hSxAITVojgQI0JH',
                'remember_token' => 'AXxMc2OySeefOwoAtjkHJbE320To3uri3Vjbf2DaOFLO5RcLSiTgbuNTo4Cx',
                'created_at' => Carbon::now(('Asia/Jakarta')),
                'updated_at' => null,
            ],

            [
                'csm_c_id' => 'client-2',
                'csm_c_secret' => '$2y$10$kJ2QLXHbgsIwUDzgLTK61u8toK56EnNIjihCjxGu4kzhN2udRkjo6',
                'csm_c_name' => 'Client 2',
                'csm_c_status' => 0,
                'csm_c_key' => '7fb4536f-baf9-4e43-aa36-b4d212777f69',
                'csm_c_bearer' => '4|7ty77aLu057YF3S31nUmpCO9K3KT2MHISgZo4xRVO9TCLfYJL41pKEXCIEsf',
                'remember_token' => 'TlSFfMHi6SAfMZZXD0405jGHEPHkTEHTlMV5ssx6FlDWd16Lf1lnhKeDuqbv',
                'created_at' => Carbon::now(('Asia/Jakarta')),
                'updated_at' => null,
            ],
            
            [
                'csm_c_id' => 'client-1',
                'csm_c_secret' => '$2y$10$kJ2QLXHbgsIwUDzgLTK61u8toK56EnNIjihCjxGu4kzhN2udRkjo6',
                'csm_c_name' => 'client 1',
                'csm_c_status' => 0,
                'csm_c_key' => '00cd1d65-bebb-46c1-ac80-f74addd013e7',
                'csm_c_bearer' => '3|EQoMb2eWiypbAqmlP7tmgQshCZr0k18FBsfq3u487mM6W4EEa7T6LIyEM3FO',
                'remember_token' => 'EiZ8JwfKde8E11aNoBts4HJTl5TsOlTelXxVwbRCTaw3KjOI94wYoGjpLsWg',
                'created_at' => Carbon::now(('Asia/Jakarta')),
                'updated_at' => null,
            ]

        ]);
    }
}
