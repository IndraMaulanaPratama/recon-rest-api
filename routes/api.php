<?php

use App\Http\Controllers\Admin\ApiManagement\ApiNameController;
use App\Http\Controllers\Admin\ApiManagement\ApiScopeController;
use App\Http\Controllers\Api\Account\CoreAccountController;
use App\Http\Controllers\Api\Bank\CoreBankController;
use App\Http\Controllers\Api\Biller\CoreBillerAccountController;
use App\Http\Controllers\Api\Biller\CoreBillerCalendarController;
use App\Http\Controllers\Api\Biller\CoreBillerController;
use App\Http\Controllers\Api\Biller\CoreBillerProductController;
use App\Http\Controllers\Api\Biller\CoreGroupBillerController;
use App\Http\Controllers\Api\Calendar\CoreCalendarController;
use App\Http\Controllers\Api\Calendar\CoreCopyCalendarController;
use App\Http\Controllers\Api\CID\CorePartnerCidCotroller;
use App\Http\Controllers\Api\CoreDownCentralController;
use App\Http\Controllers\Api\CoreTransactionDefinitionController;
use App\Http\Controllers\Api\Correction\CoreCorrectionController;
use App\Http\Controllers\Api\Exlude_Partner\CoreExludePartnerController;
use App\Http\Controllers\Api\ModuleDefinition;
use App\Http\Controllers\Api\Partner\CorePartnerController;
use App\Http\Controllers\Api\Profile\CoreProfileFeeController;
use App\Http\Controllers\Api\ReconDana\ReconDanaController;
use App\Http\Controllers\Api\Tools\ToolsController;
use App\Http\Controllers\Api\TransactionDefinition\TransactionDefinitionV2;
use App\Http\Controllers\Api\Transfer_Fund\CoreGroupProductController;
use App\Http\Controllers\Api\Transfer_Fund\CoreGroupTransferFundsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\FormulaTransfer\FormulaTransferController;
use App\Http\Controllers\ReconData\CoreReconDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::middleware(['auth:api'])->group(function () {
//   Route::post('/register', [AuthController::class, 'register']);
// });

//  *** AUTHENTICATIONS AREA
Route::middleware('auth:sanctum', 'clientRegister')->group(function () {
    Route::post('/auth/adminOnly/register', [ClientController::class, 'store']);
    Route::post('/auth/adminOnly/showClient', [ClientController::class, 'show']);
    Route::post('/auth/adminOnly/updatePasswordClient', [ClientController::class, 'updatePassword']);
});

// Route::post('/auth/adminOnly/register', [ClientController::class, 'store']);
Route::middleware('checkAuth:oauth-token')->post('/oauth/token', [AuthController::class, 'login']);


// *** API MANAGEMENT
Route::middleware('auth:sanctum', 'clientRegister')->group(function () {
    Route::get('/api-management', [ApiNameController::class, 'index']);
    Route::post('/api-management', [ApiNameController::class, 'store']);
    Route::post('/api-management/show', [ApiNameController::class, 'show']);
    Route::put('/api-management', [ApiNameController::class, 'update']);
    Route::delete('/api-management', [ApiNameController::class, 'destroy']);
    Route::get('/api-management/filter', [ApiNameController::class, 'filter']);
    Route::get('/api-scope', [ApiScopeController::class, 'index']);
    Route::post('/api-scope', [ApiScopeController::class, 'store']);
    Route::get('/api-scope/filter', [ApiScopeController::class, 'filter']);
    Route::delete('/api-scope', [ApiScopeController::class, 'destroy']);
});

// *** API Product / transaction_definition
// Route::controller(CoreTransactionDefinitionController::class)->middleware('auth:sanctum')->group(function () {
//     Route::get('/product/list/{config}', 'index')
//     ->middleware('checkAuth:product-list-config');

//     Route::post('/product/add', 'store')
//     ->middleware('checkAuth:product-add');

//     Route::post('/product/get-data/', 'show')
//     ->middleware('checkAuth:product-get-data');

//     Route::put('/product/update/{name}', 'update')
//     ->middleware('checkAuth:product-update-name');

//     Route::put('/product/delete/{id}', 'destroy')
//     ->middleware('checkAuth:product-delete-id');

//     Route::get('/product/filter/', 'filter')
//     ->middleware('checkAuth:product-filter');

//     // API Product/Area Get Trash Data
//     Route::get('/product/trash/', 'trash')
//     ->middleware('checkAuth:product-trash');

//     // API Product/Area Delete Permanent
//     Route::delete('/product/delete/{name}', 'deleteData')
//     ->middleware('checkAuth:delete-permanent');

//     // API NOMOR 7
//     Route::get('/product/get-count/', 'getCount')
//     ->middleware('checkAuth:product-get-count');

//     // API NOMOR 8 Tampilkan nama field table
//     Route::post('/product/data-column/', 'dataColumn')
//     ->middleware('checkAuth:product-data-column');

//     // API NOMOR 9 Membuat Test Request sebelum input data
//     Route::post('/product/test-data', 'testData')
//     ->middleware('checkAuth:product-test-data');
// });

// *** API Product / transaction_definition V2
Route::controller(TransactionDefinitionV2::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/product/list/{config}', 'index')
    ->middleware('checkAuth:product-list-config');

    Route::post('/product/add', 'store')
    ->middleware('checkAuth:product-add');

    Route::post('/product/get-data/', 'show')
    ->middleware('checkAuth:product-get-data');

    Route::put('/product/update/', 'update')
    ->middleware('checkAuth:product-update-name');

    Route::put('/product/delete/', 'destroy')
    ->middleware('checkAuth:product-delete-id');

    Route::get('/product/filter/', 'filter')
    ->middleware('checkAuth:product-filter');

    // API Product/Area Get Trash Data
    Route::get('/product/trash/', 'trash')
    ->middleware('checkAuth:product-trash');

    // API Product/Area Restore Trash Data
    Route::post('/product/restore/', 'restore')
    ->middleware('checkAuth:product-restore');

    // API Product/Area Delete Permanent
    Route::delete('/product/delete/', 'deleteData')
    ->middleware('checkAuth:delete-permanent');

    // API Product/Area Get Count
    Route::get('/product/get-count', 'getCount')
    ->middleware('checkAuth:product-get-count');
});

// *** API CID / Down Central
Route::controller(CoreDownCentralController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/cid/list/{config}', 'index')
    ->middleware('checkAuth:cid-list-config');

    Route::post('/cid/get-data', 'show')
    ->middleware('checkAuth:cid-get-data');

    Route::post('/cid/add', 'store')
    ->middleware('checkAuth:cid-add');

    Route::put('/cid/update/{id}', 'update')
    ->middleware('checkAuth:cid-update-id');

    Route::put('/cid/delete/{id}', 'destroy')
    ->middleware('checkAuth:cid-delete-id');

    Route::get('/cid/filter/', 'filter')
    ->middleware('checkAuth:cid-filter'); //{id}{name}{profile_id}{type}{fund_type}{terminal_type}

    Route::get('/cid/trash/', 'trash')
    ->middleware('checkAuth:cid-trash');

    // Api Nomor 16 Get Data Profile
    Route::post('/cid/data-profile', 'dataProfile')
    ->middleware('checkAuth:cid-data-profile');

    // Api Nomor 17 Buat List Data dengan Where DC_PROFILE = null
    Route::get('/cid/unmapping-profile', 'unmappingProfile')
    ->middleware('checkAuth:cid-list-unmapping-profile');

    // Api Nomor 18 Buat Update Profile yang Unmapping dan Validasi Request Input Profile ke Table Profile Fee
    Route::put('/cid/update-profile/{id}', 'updateProfile')
    ->middleware('checkAuth:cid-update-profile-id');

    // Api Nomor 19 Buat Bulk Update Profile yang Unmapping
    Route::put('/cid/many-update-profile/', 'manyUpdateProfile')
    ->middleware('checkAuth:cid-many-update-profile');

    // API Delete Permanent CID
    Route::delete('/cid/delete/{id}', 'deleteData')
    ->middleware('checkAuth:cid-delete-data');

    // API Product/Area Restore Trash Data
    Route::post('/cid/restore/', 'restore')
    ->middleware('checkAuth:cid-restore');
});

// ***  API CORE BILLER
Route::controller(CoreBillerController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/biller/list/{config}', 'index')
   ->middleware('checkAuth:biller-list-config');

    Route::post('/biller/add', 'store')
   ->middleware('checkAuth:biller-add');

    Route::post('/biller/get-data', 'show')
   ->middleware('checkAuth:biller-get-data');

    Route::put('/biller/update/{id}', 'update')
    ->middleware('checkAuth:biller-update-id');

    Route::put('/biller/delete/{id}', 'destroy')
    ->middleware('checkAuth:biller-delete-id');

    Route::get('/biller/filter', 'filter')
    ->middleware('checkAuth:biller-filter'); // ?name=&gop=GP-DELIMA

    Route::get('/biller/trash', 'trash')
   ->middleware('checkAuth:biller-trash'); // ?name=&gop=GP-DELIMA

    // Api Nomor 22 Get Data Group Of Product field CSC_GOP_PRODUCT_GROUP by distinct
    Route::get('/biller/list-gop', 'listGop')
    ->middleware('checkAuth:biller-list-gop');

    // API Delete Biller Permanent
    Route::delete('/biller/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');

    // API Biller Restore Trash Data
    Route::post('/biller/restore/', 'restore')
    ->middleware('checkAuth:biller-restore');

    // Api (Tambahan) List Biller By GOP
    Route::post('/biller/by-gop', 'billerByGop')
    ->middleware('checkAuth:biller-by-gop');

    // Api (Tambahan) List Biller By GOP
    Route::get('/biller/list-modul/{config}', 'billerListModul')
    ->middleware('checkAuth:biller-list-modul');

    // Api Biller Unmapping Profile
    Route::get('/biller/unmapping-profile', 'unmappingProfile')
    ->middleware('checkAuth:biller-unmapping-profile');

    // Api Biller Update Data Profile
    Route::put('/biller/update-profile/{biller_id}', 'updateProfile')
    ->middleware('checkAuth:biller-update-profile');
});

// ***  API CORE BILLER ACCOUNT
Route::controller(CoreBillerAccountController::class)->middleware('auth:sanctum')->group(function () {
    // Api nomor 27 Get all Account Number (rekening) Berdasarkan biller id
    Route::post('/biller/list-account', 'listAccount')
    ->middleware('checkAuth:biller-list-account');

    // Api nomor 28 Get all Account Number (rekening) & Cek Data Request account ke table account
    Route::post('/biller/data-account', 'dataAccount')
    ->middleware('checkAuth:biller-data-account');

    // Api nomor 29 create data ke table biller_account & Cek validasi data ke masing masing table
    Route::post('/biller/add-account', 'addBillerAccount')
    ->middleware('checkAuth:biller-add-account');

    // Api nomor 30 Delete data ke table biller_account
    Route::delete('/biller/delete-account/{id}', 'deleteAccount')
    ->middleware('checkAuth:biller-delete-account-id');

    // Api Tambahan Biller List Add Account
    Route::get('/biller/list-add-account/', 'listAddAccount')
    ->middleware('checkAuth:biller-list-add-account');
});

// *** API CORE BILLER PRODUCT
Route::controller(CoreBillerProductController::class)->middleware('auth:sanctum')->group(function () {
    // Api nomor 31 Get List Product Biller
    Route::post('/biller/list-product', 'index')
    ->middleware('checkAuth:biller-list-product');

    // Api nomor 32 List Add Product Biller
    Route::post('/biller/list-add-product', 'listAddProduct')
    ->middleware('checkAuth:biller-list-add-product');

    // Api nomor 33 Add Product Biller
    Route::post('/biller/add-product', 'store')
    ->middleware('checkAuth:biller-product-add');

    // Api nomor 33 Delete data Product Account
    Route::delete('/biller/delete-product/{id}', 'destroy')
    ->middleware('checkAuth:biller-delete-product-id');
});

// *** API CORE BILLER CALENDAR
Route::controller(CoreBillerCalendarController::class)->middleware('auth:sanctum')->group(function () {
    // Api nomor 35 Get List Biller Calendar
    Route::post('/biller/list-calendar', 'index')
    ->middleware('checkAuth:biller-list-calendar');

    // Api nomor 36 Get Get Data Calendar
    Route::post('/biller/data-calendar', 'show')
    ->middleware('checkAuth:biller-data-calendar');

    // Api nomor 37 Add Data Calendar
    Route::post('/biller/add-calendar', 'store')
    ->middleware('checkAuth:biller-add-calendar');

    // Api Nomor 39 Delete Calendar
    Route::delete('/biller/delete-calendar/{id}', 'destroy')
    ->middleware('checkAuth:biller-delete-calendar');
});

// *** API CORE  PARTNER
Route::controller(CorePartnerController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/partner/list/{config}', 'index')
    ->middleware('checkAuth:partner-list-config');

    // Api No. 42 Add Partner
    Route::post('/partner/add', 'store')
    ->middleware('checkAuth:partner-add');

    Route::post('/partner/get-data', 'show')
    ->middleware('checkAuth:partner-get-data');

    Route::put('/partner/update/{id}', 'update')
    ->middleware('checkAuth:partner-update-id');

    Route::put('/partner/delete/{id}', 'destroy')
    ->middleware('checkAuth:partner-delete-id');

    // API Partner Restore Trash Data
    Route::post('/partner/restore/', 'restore')
    ->middleware('checkAuth:partner-restore');

    Route::get('/partner/filter', 'filter')
    ->middleware('checkAuth:partner-filter');

    Route::get('/partner/trash', 'trash')
    ->middleware('checkAuth:partner-trash');

    Route::delete('/partner/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');
});

// *** API PARTNER CID
Route::controller(CorePartnerCidCotroller::class)->middleware('auth:sanctum')->group(function () {
    // Api No 46 Partner List CID
    Route::post('/partner/list-cid', 'index')
    ->middleware('checkAuth:partner-list-cid');

    // Api No 47 Add Partner CID
    Route::post('/partner/add-cid', 'store')
    ->middleware('checkAuth:partner-add-cid');

    // Api No 48 Delete Partner CID
    Route::delete('/partner/delete-cid/{id}', 'destroy')
    ->middleware('checkAuth:partner-delete-cid-id');

    // Api No 49 Get Unmapping Partner CID
    Route::get('/partner/unmapping-cid', 'unmappingCid')
    ->middleware('checkAuth:partner-unmapping-cid');

    // Api No 50 Add Unmapping Partner CID
    Route::post('/partner/add-unmapping-cid', 'addunmappingCid')
    ->middleware('checkAuth:partner-update-unmapping-cid');
});

// *** API CORE CORE BANK
Route::controller(CoreBankController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/bank/list/{config}', 'index')
    ->middleware('checkAuth:bank-list-config');

    Route::post('/bank/add', 'store')
    ->middleware('checkAuth:bank-add');

    Route::post('/bank/get-data', 'show')
    ->middleware('checkAuth:bank-get-data');

    Route::put('/bank/update/{id}', 'update')
    ->middleware('checkAuth:bank-update-id');

    Route::put('/bank/delete/{id}', 'destroy')
    ->middleware('checkAuth:bank-delete-id');

    // API Bank Restore Trash Data
    Route::post('/bank/restore/', 'restore')
    ->middleware('checkAuth:bank-restore');

    Route::get('/bank/filter', 'filter')
    ->middleware('checkAuth:bank-filter'); // ?name=data-

    Route::get('/bank/trash', 'trash')
    ->middleware('checkAuth:bank-trash'); // ?name=data-

    Route::delete('bank/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');
});

// *** API CORE ACCOUNT
Route::controller(CoreAccountController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/account/list/{config}', 'index')
    ->middleware('checkAuth:account-list-config');

    Route::post('/account/add', 'store')
    ->middleware('checkAuth:account-add');

    Route::post('/account/get-data', 'show')
    ->middleware('checkAuth:account-get-data');

    Route::put('/account/update/{id}', 'update')
    ->middleware('checkAuth:account-update-id');

    Route::put('/account/delete/{id}', 'destroy')
    ->middleware('checkAuth:account-delete-id');

    Route::get('/account/filter', 'filter')
    ->middleware('checkAuth:account-filter'); // ?number=&bank=DANAMON&name=&owner=&type=0

    Route::get('/account/trash', 'trash')
    ->middleware('checkAuth:account-trash'); // ?number=&bank=DANAMON&name=&owner=&type=0

    Route::delete('/account/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');

    // API Account Restore Trash Data
    Route::post('/account/restore/', 'restore')
    ->middleware('checkAuth:account-restore');
});

// *** API CORE GROUP BILLER
Route::controller(CoreGroupBillerController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/group-biller/list/{config}', 'index')
    ->middleware('checkAuth:group-biller-list-config');

    // API No 65 Add Group Biller
    Route::post('/group-biller/add', 'store')
    ->middleware('checkAuth:group-biller-add');

    Route::post('/group-biller/get-data', 'show')
    ->middleware('checkAuth:group-biller-get-data');

    Route::put('/group-biller/update/{id}', 'update')
    ->middleware('checkAuth:group-biller-update-id');

    Route::put('/group-biller/delete/{id}', 'destroy')
    ->middleware('checkAuth:group-biller-delete-id');

    // API Group Biller Restore Trash Data
    Route::post('/group-biller/restore/', 'restore')
    ->middleware('checkAuth:group-biller-restore');

    Route::get('/group-biller/filter', 'filter')
    ->middleware('checkAuth:group-biller-filter'); // ?name=&gop=GP-DELIMA

    Route::get('/group-biller/trash', 'trash')
    ->middleware('checkAuth:group-biller-trash'); // ?name=&gop=GP-DELIMA

    // API No: 69 Get List Biller-Group Biller
    Route::post('/group-biller/list-biller', 'listBiller')
    ->middleware('checkAuth:group-biller-list-biller');

    // API No: 70 Get List Add Biller-Group Biller
    Route::post('/group-biller/list-add-biller/{config}', 'listAddBiller')
    ->middleware('checkAuth:group-biller-list-add-biller');


    // API No: 71 Add Biller-Group Biller
    Route::post('/group-biller/add-biller', 'addBiller')
    ->middleware('checkAuth:group-biller-add-biller');

    // API No: 72 Delete Biller-Group Biller (Permanent)
    Route::delete('/group-biller/delete-biller/{id}', 'deleteBiller')
    ->middleware('checkAuth:group-biller-delete-biller');

    // API Delete Group Biller (Permanent)
    Route::delete('/group-biller/delete/{id}', 'deleteData')
    ->middleware('checkAuth:group-biller-delete-data');
});

// *** API CORE GROUP TRANSFER FUNDS
Route::controller(CoreGroupTransferFundsController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/group-funds/list/{config}', 'index')
    ->middleware('checkAuth:group-funds-list-config');

    // API No 75 Add Group Transfer Funds
    Route::post('/group-funds/add', 'store')
    ->middleware('checkAuth:group-funds-add');

    Route::post('/group-funds/get-data', 'show')
    ->middleware('checkAuth:group-funds-get-data');

    Route::put('/group-funds/update/{id}', 'update')
    ->middleware('checkAuth:group-funds-update-id');

    Route::put('/group-funds/delete/{id}', 'destroy')
    ->middleware('checkAuth:group-funds-delete-id');

    // API Group Funds Restore Trash Data
    Route::post('/group-funds/restore/', 'restore')
    ->middleware('checkAuth:group-funds-restore');

    Route::get('/group-funds/filter', 'filter')
    ->middleware('checkAuth:group-funds-filter'); // ?account_src=&account_dest&type=0

    Route::get('/group-funds/trash', 'trash')
    ->middleware('checkAuth:group-funds-trash');

    Route::delete('/group-funds/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');

    Route::post('/group-funds/by-biller', 'byBiller')
    ->middleware('checkAuth:group-funds-by-biller');

    // Api Tambahan Group Transfer Funds Get Amount
    Route::post('group-funds/get-amount', 'getAmount')
    ->middleware('checkAuth:group-funds-get-amount');
});

// *** CORE PRODUCT GROUP Product
Route::controller(CoreGroupProductController::class)->middleware('auth:sanctum')->group(function () {
    // Api No 79 Get List Product-Group Transfer Fund
    Route::post('/group-funds/list-product', 'listProduct')
    ->middleware('checkAuth:group-list-product');

    // Api No 80 Get List Add Product-Group Transfer Fund
    Route::post('/group-funds/list-add-product/{config}', 'listAddProduct')
    ->middleware('checkAuth:group-list-add-product');

    // Api No 81 Add Product-Group Transfer Fund
    Route::post('/group-funds/add-product', 'addProduct')
    ->middleware('checkAuth:group-add-product');

    // Api No 82 Delete Product-Group Transfer Fund
    Route::delete('/group-funds/delete-product/{id}', 'deleteProduct')
    ->middleware('checkAuth:group-delete-product');
});

// *** CORE EXCLUDE PARTNER
Route::controller(CoreExludePartnerController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/exclude-partner/list/{config}', 'index')
    ->middleware('checkAuth:exclude-partner-list-config');

    // API No. 85 Add Excude Partner
    Route::post('/exclude-partner/add', 'store')
    ->middleware('checkAuth:exclude-partner-add');

    Route::delete('/exclude-partner/delete/{id}', 'destroy')
    ->middleware('checkAuth:exclude-partner-delete-id');

    Route::get('/exclude-partner/filter', 'filter')
    ->middleware('checkAuth:exclude-partner-filter'); // ?cid=&cid_name=&product=&items=50
});

// *** CORE CALENDAR
Route::controller(CoreCalendarController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/calendar/list/{config}', 'index')
    ->middleware('checkAuth:calendar-list-config');

    Route::post('/calendar/add', 'store')
    ->middleware('checkAuth:calendar-add');

    Route::post('/calendar/get-data', 'show')
    ->middleware('checkAuth:calendar-get-data');

    // Api Nomor 38 Detail Calendar View
    Route::post('/calendar/view', 'view')
    ->middleware('checkAuth:calendar-calendar-view');

    Route::put('/calendar/update/{id}', 'update')
    ->middleware('checkAuth:calendar-update-id');

    Route::put('/calendar/delete/{id}', 'destroy')
    ->middleware('checkAuth:calendar-delete-id');

    // API Calendar Restore Trash Data
    Route::post('/calendar/restore/', 'restore')
    ->middleware('checkAuth:calendar-restore');

    Route::get('/calendar/filter', 'filter')
    ->middleware('checkAuth:calendar-filter'); // ?name=libur&items=50

    Route::get('/calendar/trash', 'trash')
    ->middleware('checkAuth:calendar-trash'); // ?name=libur&items=50

    Route::put('/calendar/set-default/{id}', 'setDefault')
    ->middleware('checkAuth:calendar-set-default-id');

    // API Delete Permanent Calendar
    Route::delete('calendar/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');
});

// *** CORE COPY CALENDAR
Route::controller(CoreCopyCalendarController::class)->middleware('auth:sanctum')->group(function () {
    // Api No 93 Get All Data Calendar Copy
    Route::post('/calendar/get-data-copy', 'getDataCopy')
    ->middleware('checkAuth:calendar-get-data-copy');

    // Api No 94 Add Calendar Copy
    Route::post('/calendar/copy', 'calendarCopy')
    ->middleware('checkAuth:calendar-copy');

    // Api No 96 Calendar List Day
    Route::post('/calendar/list-day/', 'listDay')
    ->middleware('checkAuth:calendar-list-day');

    // Api No 97 Add Day
    Route::post('/calendar/add-day/', 'addDay')
    ->middleware('checkAuth:calendar-add-day');

    // Api No 98 Get Data Days Calendar
    Route::post('/calendar/get-data-day/', 'getDataDay')
    ->middleware('checkAuth:calendar-get-data-day');

    // Api No 99 Get Update Calendar Day
    Route::put('/calendar/update-day/{id}', 'updateDay')
    ->middleware('checkAuth:calendar-update-day');

    // Api No 100 Delete Calendar Day
    Route::delete('/calendar/delete-day/{id}', 'deleteDay')
    ->middleware('checkAuth:calendar-delete-day');
});

// *** API CORE PROFILE FEE
Route::controller(CoreProfileFeeController::class)->middleware('auth:sanctum')->group(function () {
    // APi No. 103 Get List Profile Fee
    Route::get('/profile/list/{config}', 'index')
    ->middleware('checkAuth:profile-list-config');

    Route::post('/profile/add', 'store')
    ->middleware('checkAuth:profile-add');

    Route::post('/profile/get-data', 'show')
    ->middleware('checkAuth:profile-get-data');

    Route::put('/profile/update/{id}', 'update')
    ->middleware('checkAuth:profile-update-id');

    // Api No 133 Set Default Profile Fee
    Route::put('/profile/set-default/{id}', 'setDefault')
    ->middleware('checkAuth:profile-set-default-id');

    Route::put('/profile/delete/{id}', 'destroy')
    ->middleware('checkAuth:profile-delete-id');

    // API Profile Fee Restore Trash Data
    Route::post('/profile/restore/', 'restore')
    ->middleware('checkAuth:profile-restore');

    // APi No. 116 Filter Profie Fee
    Route::get('/profile/filter', 'filter')
    ->middleware('checkAuth:profile-filter'); // ?name=PROFILE&items=50

    Route::get('/profile/trash', 'trash')
    ->middleware('checkAuth:profile-trash'); // ?name=PROFILE&items=50

    // Api No. 102
    Route::post('/profile/get-count-product', 'getCountProduct')
    ->middleware('checkAuth:profile-get-count-product');

    // Api No. 108 Get Data Copy
    Route::post('/profile/get-data-copy', 'getDataCopy')
    ->middleware('checkAuth:profile-get-data-copy');

    // Api No. 109 Copy Data Profile Fee
    Route::post('/profile/copy', 'copyData')
    ->middleware('checkAuth:profile-copy');

    // Api No. 111 Get Product Profile Fee
    Route::post('/profile/list-product', 'getListProductProfileFee')
    ->middleware('checkAuth:profile-list-product');

    // Api No. 112 Profile Add Product
    Route::post('/profile/add-product', 'profileAddProduct')
    ->middleware('checkAuth:profile-profile-add-product');

    // Api No. 113 Get Data Product Profile Fee
    Route::post('/profile/get-data-product', 'getDataProduct')
    ->middleware('checkAuth:profile-get-data-product');

    // Api No. 114 Update Product Profile Fee
    Route::put('/profile/update-product/{id}', 'updateProductProfile')
    ->middleware('checkAuth:profile-update-product-id');

    // Api No. 115 Delete Product Profile Fee
    Route::delete('/profile/delete-product/{id}', 'deleteProductProfile')
    ->middleware('checkAuth:profile-delete-product-id');

    // Api Delete Permanent Profile-Fee
    Route::delete('/profile/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');

    // Api Tambahan Product Not Existing
    Route::get('profile/list-product-unexists', 'productUnexists')
    ->middleware('checkAuth:profile-list-product-unexists');
});

// *** CORE CORRECTION
Route::controller(CoreCorrectionController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/correction/list/{config}', 'index')
    ->middleware('checkAuth:correction-list-config');

    Route::post('/correction/add', 'store')
    ->middleware('checkAuth:correction-add');

    // API No. 119 Get Data Correction
    Route::post('/correction/get-data', 'show')
    ->middleware('checkAuth:correction-get-data');

    Route::put('/correction/update/{id}', 'update')
    ->middleware('checkAuth:correction-update-id');

    Route::put('/correction/delete/{id}', 'destroy')
    ->middleware('checkAuth:correction-delete-id');

    Route::get('/correction/filter', 'filter')
    ->middleware('checkAuth:correction-filter');

    Route::get('/correction/trash', 'trash')
    ->middleware('checkAuth:correction-trash');

    // Api Delete Permanent Correction
    Route::delete('/correction/delete/{id}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');

    // API Correction Restore Trash Data
    Route::post('/correction/restore/', 'restore')
    ->middleware('checkAuth:correction-restore');
});

// *** CORE TOOLS
Route::controller(ToolsController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/tools/get-modul', 'getModul')
    ->middleware('checkAuth:tools-modul-getData'); // ?group_transfer=GP-UTAMA&items=50

    Route::post('/tools/get-biller', 'getBiller')
    ->middleware('checkAuth:tools-biller-getData'); // ?group_transfer=GP-UTAMA&items=50

    Route::post('/tools/get-transfer-funds', 'getTransferFunds')
    ->middleware('checkAuth:tools-transfer-funds-getData'); // ?group_transfer=GP-UTAMA&items=50
});

// *** Core Formula Transfer
Route::controller(FormulaTransferController::class)->middleware('auth:sanctum')->group(function () {
    // Api Tambahan Formula Transfer Get List
    Route::get('/formula-transfer/list/{cofig}', 'getList')
    ->middleware('checkAuth:formula-transfer-get-list-config');

    // Api Tambahan Formula Transfer Get Data
    Route::post('/formula-transfer/get-data/', 'getData')
    ->middleware('checkAuth:formula-transfer-get-data');

    // Api Tambahan Formula Transfer Add Data
    Route::post('/formula-transfer/add/', 'addData')
    ->middleware('checkAuth:formula-transfer-add');

    // Api Tambahan Formula Transfer Update Data
    Route::put('/formula-transfer/update/{id}', 'updateData')
    ->middleware('checkAuth:formula-transfer-update-id');

    // Api Tambahan Formula Transfer Delete Data
    Route::put('/formula-transfer/delete/{id}', 'deleteData')
    ->middleware('checkAuth:formula-transfer-delete-id');

    // Api Tambahan Formula Transfer Destroy Data
    Route::delete('/formula-transfer/delete/{id}', 'destroyData')
    ->middleware('checkAuth:delete-permanent');

    // Api Tambahan Formula Transfer Filter Data
    Route::get('/formula-transfer/filter/', 'filterData')
    ->middleware('checkAuth:formula-transfer-filter');

    // API Get Trash Formula Transfer
    Route::get('/formula-transfer/trash', 'trash')
    ->middleware('checkAuth:formula-transfer-trash');

    // API Restore Data Formula Transfer
    Route::post('/formula-transfer/restore', 'restore')
    ->middleware('checkAuth:formula-transfer-restore');
});

// *** CORE RECON DATA
Route::controller(CoreReconDataController::class)->middleware('auth:sanctum')->group(function () {
    // API 123 List Recon Data
    Route::get('/recon-data/list', 'list')
    ->middleware('checkAuth:recon-data-list');

    // API Reccon Data Filter
    Route::get('/recon-data/filter', 'filter')
    ->middleware('checkAuth:recon-data-filter');

    // API 124 SETLED RECON DATA
    Route::post('/recon-data/settled', 'settledProduct')
    ->middleware('checkAuth:recon-data-settled');

    // API 125 Get List Suspect Recon data
    Route::post('/recon-data/list-suspect', 'listSuspect')
    ->middleware('checkAuth:recon-data-list-suspect');

    // API 126 Get List By Product Recon Data
    Route::post('/recon-data/by-product', 'listByProduct')
    ->middleware('checkAuth:recon-data-by-product');

    // Recon Data List By CID
    Route::post('/recon-data/by-cid', 'listByCid')
    ->middleware('checkAuth:recon-data-by-cid');

    // Recon Data Get Data History
    Route::post('/recon-data/history', 'history')
    ->middleware('checkAuth:recon-data-history');

    // Recon Data Export
    Route::post('/recon-data/export', 'export')
    ->middleware('checkAuth:recon-data-export');

    // Recon Data Download
    Route::get('/recon-data/export-download/{id}', 'download')
    ->middleware('checkAuth:recon-data-download');
});

// *** CORE RECON DANA
Route::controller(ReconDanaController::class)->middleware(('auth:sanctum'))->group(function () {
    // API Recon Dana Get Unmapping Group Biller-Biller
    Route::get('/recon-dana/unmapping-biller', 'unmappingBiller')
    ->middleware("checkAuth:recon-dana-unmapping-biller");

    // API Recon Dana Get Unmapping Product
    Route::get('/recon-dana/unmapping-product', 'unmappingProduct')
    ->middleware('checkAuth:recon-dana-unmapping-product');

    // API Recon Dana Add Group Biller-Biller
    Route::post('/recon-dana/add-biller', 'addBiller')
    ->middleware("checkAuth:recon-dana-add-biller");

    // API Recon Dana Add Data Group Transfer Product
    Route::post('/recon-dana/add-product', 'addProduct')
    ->middleware('checkAuth:recon-dana-add-producr');

    // Api Recon Dana Recon Dana Process
    Route::post('/recon-dana/process/', 'process')
    ->middleware('checkAuth:recon-dana-process');

    // Api Recon Dana Get List Correction Settled
    Route::post('recon-dana/list-correction-process', 'listCorrectionProcess')
    ->middleware('checkAuth:recon-dana-list-correction-process');

    // API Recon Dana Update Correction Settled
    Route::put('recon-dana/update-correction-process', 'updateCorrectionProcess')
    ->middleware('checkAuth:recon-dana-update-correction-process');

    // Api Recon Dana Get List Suspect Recon Dana
    Route::post('recon-dana/list-suspect-process', 'listSuspectProcess')
    ->middleware('checkAuth:recon-dana-list-suspect-process');

    // Api Recon Dana Update Data Suspect Settled
    Route::put('recon-dana/update-suspect-process', 'updateSuspectProcess')
    ->middleware('checkAuth:recon-dana-update-suspect-process');

    // API Recon Dana List Summary
    Route::get('recon-dana/list-summary', 'listSummary')
    ->middleware('checkAuth:recon-dana-list-summary');

    // API Recon Dana List Recon
    Route::get('recon-dana/list', 'list')
    ->middleware('checkAuth:recon-dana-list');

    // API Recon Dana Filter Data
    Route::get('recon-dana/filter', 'filter')
    ->middleware('checkAuth:recon-dana-filter');

    // Api Recon Dana List Correction
    Route::post('recon-dana/list-correction', 'listCorrection')
    ->middleware('checkAuth:recon-dana-list-correction');

    // Api Recon Dana List Suspect
    Route::post('recon-dana/list-suspect', 'listSuspect')
    ->middleware('checkAuth:recon-dana-list-suspect');

    // Api Recon Dana Get List By ID
    Route::post('recon-dana/by-id', 'byId')
    ->middleware('checkAuth:recon-dana-list-by-id');

    // Api Recon Dana By Id Suspect
    Route::post('recon-dana/by-id-suspect', 'byIdSuspect')
    ->middleware('checkAuth:recon-dana-list-by-id-suspect');

    // Api Recon Dana Get Suspect By Product
    Route::post('recon-dana/list-suspect-product', 'listSuspectProduct')
    ->middleware('checkAuth:recon-dana-list-suspect-product');

    // Api Recon Dana Get Suspect By Product
    Route::post('recon-dana/by-product', 'byProduct')
    ->middleware('checkAuth:recon-dana-list-by-product');

    // Api Recon Dana Get List By CID
    Route::post('recon-dana/by-cid', 'listByCid')
    ->middleware('checkAuth:recon-dana-list-by-cid');

    // API Recon Dana Get List History
    Route::post('recon-dana/history', 'history')
    ->middleware('checkAuth:recon-dana-history');

    // API Recon Dana Different Transfer
    Route::post('recon-dana/get-diff-transfer', 'diffTransfer')
    ->middleware('checkAuth:recon-dana-diff-transfer');

    // API Recon Dana Add Different Transfer
    Route::post('recon-dana/add-diff-transfer', 'addDiffTransfer')
    ->middleware('checkAuth:recon-dana-add-diff-transfer');

    // API Recon Dana Get Additional Transfer
    Route::post('recon-dana/get-transfer', 'getTransfer')
    ->middleware('checkAuth:recon-dana-get-transfer');

    // API Recon Dana Add Additional Transfer
    Route::post('recon-dana/add-transfer', 'addTransfer')
    ->middleware('checkAuth:recon-dana-add-transfer');

    // API Recon Dana Export
    Route::post('recon-dana/export', 'export')
    ->middleware('checkAuth:recon-dana-export');
});

// *** MODULE DEFINITION
Route::controller(ModuleDefinition::class)->middleware(('auth:sanctum'))->group(function () {
    // API Module Definition Get List Config
    Route::get('product-module/list/{config}', 'index')
    ->middleware('checkAuth:module-definition-list-config');

    // API Module Definition Add Data
    Route::post('product-module/add', 'store')
    ->middleware('checkAuth:module-definition-add');

    // API Module Definition Get Data
    Route::post('product-module/get-data', 'show')
    ->middleware('checkAuth:module-definition-get-data');

    // API Module Definition Update
    Route::put('product-module/update/{groupname}', 'update')
    ->middleware('checkAuth:module-definition-update');

    // API Module Definition Update
    Route::put('product-module/delete/{groupname}', 'destroy')
    ->middleware('checkAuth:module-delete');

    // API Module Definition Data Column
    Route::post('product-module/data-column', 'dataColumn')
    ->middleware('checkAuth:module-data-column');

    // API Module Definition Test Data
    Route::post('product-module/test-data', 'testData')
    ->middleware('checkAuth:module-test-data');

    // API Module Definition Filter
    Route::get('product-module/filter', 'filter')
    ->middleware('checkAuth:module-filter');

    // API Module Definition Get Trash Data
    Route::get('/product-module/trash/', 'trash')
    ->middleware('checkAuth:product-trash');

    // API Module Definition Restore Trash Data
    Route::post('/product-module/restore/', 'restore')
    ->middleware('checkAuth:product-restore');

    // API Module Definition Delete Permanent
    Route::delete('/product-module/delete/{name}', 'deleteData')
    ->middleware('checkAuth:delete-permanent');
});
