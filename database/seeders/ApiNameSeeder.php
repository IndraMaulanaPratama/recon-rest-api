<?php

namespace Database\Seeders;

use App\Models\ApiName;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ApiName::insert([
            [
                'CSM_AN_ID' => 'api-management',
                'CSM_AN_DESC' => null,
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null,
            ],

            [
                'CSM_AN_ID' => 'api-management-show',
                'CSM_AN_DESC' => null,
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'auth-adminOnly-register',
                'CSM_AN_DESC' => null,
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'auth-adminOnly-showClient',
                'CSM_AN_DESC' => null,
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'auth-adminOnly-updatePasswordClient',
                'CSM_AN_DESC' => null,
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'cid-add',
                'CSM_AN_DESC' => 'End Point Create A New CID Record',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'cid-delete-id',
                'CSM_AN_DESC' => 'End Point Delete Record CID Base On Id',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'cid-filter',
                'CSM_AN_DESC' => 'End Point Show List CID Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'cid-get-data',
                'CSM_AN_DESC' => 'End Point Show List CID Data Base On Data Parameter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'cid-list-config',
                'CSM_AN_DESC' => 'End Point Show List CID Data Base On Config',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'cid-update-id',
                'CSM_AN_DESC' => 'End Point Update Record CID Base On Id',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'oauth-token',
                'CSM_AN_DESC' => 'End Point Authenticate/Login user',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],


            [
                'CSM_AN_ID' => 'product-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Product',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'product-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Product Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'product-filter',
                'CSM_AN_DESC' => 'End Point Show List Product Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'product-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Product',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'product-list-config',
                'CSM_AN_DESC' => 'End Point Get List Product Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'product-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Product Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // --- 

            [
                'CSM_AN_ID' => 'biller-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Biller',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'biller-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Biller Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'biller-filter',
                'CSM_AN_DESC' => 'End Point Show List Biller Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'biller-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Biller',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'biller-list-config',
                'CSM_AN_DESC' => 'End Point Get List Biller Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'biller-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Biller Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'biller-trash',
                'CSM_AN_DESC' => 'End Point Get Deleted Data Biller',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // ---

            [
                'CSM_AN_ID' => 'partner-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Partner',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'partner-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Partner Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'partner-filter',
                'CSM_AN_DESC' => 'End Point Show List Partner Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'partner-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Partner',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'partner-list-config',
                'CSM_AN_DESC' => 'End Point Get List Partner Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'partner-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Partner Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'partner-trash',
                'CSM_AN_DESC' => 'End Point Get Deleted Data Partner',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // ----

            [
                'CSM_AN_ID' => 'bank-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Bank',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'bank-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Bank Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'bank-filter',
                'CSM_AN_DESC' => 'End Point Show List Bank Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'bank-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Bank',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'bank-list-config',
                'CSM_AN_DESC' => 'End Point Get List Bank Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'bank-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Bank Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'bank-trash',
                'CSM_AN_DESC' => 'End Point Get Deleted Data Bank',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // ----

            [
                'CSM_AN_ID' => 'account-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Account',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'account-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Account Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'account-filter',
                'CSM_AN_DESC' => 'End Point Show List Account Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'account-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Account',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'account-list-config',
                'CSM_AN_DESC' => 'End Point Get List Account Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'account-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Account Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'account-trash',
                'CSM_AN_DESC' => 'End Point Get Deleted Data Account',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // ----

            [
                'CSM_AN_ID' => 'group-biller-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Group Biller',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-biller-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Group Biller Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-biller-filter',
                'CSM_AN_DESC' => 'End Point Show List Group Biller Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-biller-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Group Biller',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-biller-list-config',
                'CSM_AN_DESC' => 'End Point Get List Group Biller Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-biller-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Group Biller Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-biller-trash',
                'CSM_AN_DESC' => 'End Point Get Deleted Data Group Biller',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // ----

            [
                'CSM_AN_ID' => 'group-funds-add',
                'CSM_AN_DESC' => 'End Point Create A New Record Of Group Funds',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-funds-delete-id',
                'CSM_AN_DESC' => 'End Point Deleting Data Group Funds Base On id Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-funds-filter',
                'CSM_AN_DESC' => 'End Point Show List Group Funds Data Base On Filter',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-funds-get-data',
                'CSM_AN_DESC' => 'End Point Get Specific Data Group Funds',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-funds-list-config',
                'CSM_AN_DESC' => 'End Point Get List Group Funds Base On Config Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-funds-update-name',
                'CSM_AN_DESC' => 'End Point Updating Data Group Funds Base On Name Value',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            [
                'CSM_AN_ID' => 'group-funds-trash',
                'CSM_AN_DESC' => 'End Point Get Deleted Data Group Funds',
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null
            ],

            // ----

            // [
            //     'CSM_AN_ID' => '',
            //     'CSM_AN_DESC' => null,
            //     'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            //     'CSM_AN_DELETED_DT' => null
            // ],

        ]);
    }
}
