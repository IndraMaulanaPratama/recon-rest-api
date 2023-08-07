<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class GroupTransferCest
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated('1|zlsttvwZE1JX1rmkfrtPUmQmUSkyaWvNPBd21mwRQGLkJgrU4DjXlAIW1m7j');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('x-auth-key', '2d398821-e738-4e77-b766-67e705aa6ae9');
    }

    // tests
    public function listSimple(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/group-funds/list/simple?modul=0&biller=0');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Group Transfer Funds Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/group-funds/list/detail?modul=0&biller=0&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Group Transfer Funds Success',
                'config' => 'detail',
                'result_data' => ['per_page' => 50]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I) // All belum ada
    {
        $response = $I->sendGetAsJson('/group-funds/filter?modul=0&biller=0&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Group Transfer Funds Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I) // All belum ada
    {
        $response = $I->sendGetAsJson('/group-funds/trash?modul=0&biller=0&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Group Transfer Funds Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function getAmountSuccess(ApiTester $I)
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '33333333333',
            'CSC_GTF_DESTINATION' => '44444444444',
            'CSC_GTF_NAME' => 'GTF DUMMY',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-12'
        ];
        $data_recon = [
            'CSC_RDN_ID' => '1',
            'CSC_RDN_BILLER' => 'DUMMY',
            'CSC_RDN_GROUP_TRANSFER' => 'DUMMY',
            'CSC_RDN_START_DT' => '2023-01-11',
            'CSC_RDN_END_DT' => '2023-01-11',
            'CSC_RDN_SETTLED_DT' => '2023-01-12',
            'CSC_RDN_AMOUNT' => 10000,
            'CSC_RDN_AMOUNT_TRANSFER' => 10000,
            'CSC_RDN_STATUS' => 3
        ];
        $count = $I->grabNumRecords('CSCCORE_RECON_DANA', ['CSC_RDN_ID' => '123456']);
        $countTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($countTransfer == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($countTransfer == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_RECON_DANA', $data_recon);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/get-amount', [
                    'group_transfer' => 'DUMMY',
                    'settled_date' => '2023-01-12'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Amount Recon Dana Success']);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Amount Recon Dana Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getAmountGroupTransferNotFound(ApiTester $I)
    {
        $data_recon = [
            'CSC_RDN_ID' => '123456',
            'CSC_RDN_BILLER' => 'DUMMY',
            'CSC_RDN_GROUP_TRANSFER' => 'DUMMY',
            'CSC_RDN_START_DT' => '2023-01-11',
            'CSC_RDN_END_DT' => '2023-01-11',
            'CSC_RDN_SETTLED_DT' => '2023-01-12',
            'CSC_RDN_AMOUNT' => 10000,
            'CSC_RDN_AMOUNT_TRANSFER' => 10000,
            'CSC_RDN_STATUS' => 2
        ];
        $count = $I->grabNumRecords('CSCCORE_RECON_DANA', ['CSC_RDN_ID' => '123456']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_RECON_DANA', $data_recon);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/get-amount', [
                    'group_transfer' => 'DUMMY',
                    'settled_date' => '2023-01-12'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Amount Recon Dana Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function getAmountNotFound(ApiTester $I)
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '33333333333',
            'CSC_GTF_DESTINATION' => '44444444444',
            'CSC_GTF_NAME' => 'GTF DUMMY',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-12'
        ];
        $data_recon = [
            'CSC_RDN_ID' => '1',
            'CSC_RDN_BILLER' => 'DUMMY',
            'CSC_RDN_GROUP_TRANSFER' => 'DUMMY',
            'CSC_RDN_START_DT' => '2023-01-11',
            'CSC_RDN_END_DT' => '2023-01-11',
            'CSC_RDN_SETTLED_DT' => '2023-01-12',
            'CSC_RDN_AMOUNT' => 10000,
            'CSC_RDN_AMOUNT_TRANSFER' => 10000,
            'CSC_RDN_STATUS' => 2
        ];
        $count = $I->grabNumRecords('CSCCORE_RECON_DANA', ['CSC_RDN_ID' => '123456']);
        $countTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($countTransfer == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($countTransfer == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_RECON_DANA', $data_recon);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/get-amount', [
                    'group_transfer' => 'DUMMY',
                    'settled_date' => '2023-01-20'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Amount Recon Dana Not Found']);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Amount Recon Dana Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function getAmountInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/get-amount', [
            'group_transfer' => 'DUMMY123456789012345678901234567890123456789012345678901234567890',
            'settled_date' => '2023-01-20 1234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'group_transfer' => array('The group transfer must not be greater than 50 characters.'),
                    'settled_date' => array('The settled date does not match the format Y-m-d.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getAmountInvalidDate(ApiTester $I)
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '33333333333',
            'CSC_GTF_DESTINATION' => '44444444444',
            'CSC_GTF_NAME' => 'GTF DUMMY',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-12'
        ];
        $countTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($countTransfer == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($countTransfer == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                $response = $I->sendPostAsJson('/group-funds/get-amount', [
                    'group_transfer' => 'DUMMY',
                    'settled_date' => '20-01-2023'
                ]);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson([
                        'result_data' => [
                            'settled_date' => array('The settled date does not match the format Y-m-d.')
                        ]
                    ]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function getAmountInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/get-amount', [
            'group_transfer' => '',
            'settled_date' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'group_transfer' => array('The group transfer field is required.'),
                    'settled_date' => array('The settled date field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listGroupTransfer(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '33333333333',
            'CSC_GTF_DESTINATION' => '44444444444',
            'CSC_GTF_NAME' => 'GTF DUMMY',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-12'
        ];
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_biller_product = [
            'CSC_BP_ID' => 'DUMMY',
            'CSC_BP_PRODUCT' => 'DUMMY',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $data_product_funds = [
            'CSC_PF_ID' => 'DUMMY',
            'CSC_PF_PRODUCT' => 'DUMMY',
            'CSC_PF_GROUP_TRANSFER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countBillerProduct = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_ID' => 'DUMMY']);
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countProductFunds = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_ID' => 'DUMMY']);
        if ($countBiller == 0) {
            $loop = 1;
        } else if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countProduct == 0) {
                    $loop2 = 1;
                } else if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countBillerProduct == 0) {
                    $loop3 = 1;
                } else if ($countBillerProduct == 1) {
                    $I->sendDelete('/biller/delete-product/DUMMY');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data_biller_product);
                if ($countGroupTransfer == 0) {
                    $loop4 = 1;
                } else if ($countGroupTransfer == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                if ($countProductFunds == 0) {
                    $loop5 = 1;
                } else if ($countProductFunds == 1) {
                    $I->sendDelete('/group-funds/delete-product/DUMMY');
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $I->haveInDatabase('CSCCORE_PRODUCT_FUNDS', $data_product_funds);
                $response = $I->sendPostAsJson('/group-funds/by-biller', [
                    'biller_id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Success']);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/biller/delete-product/DUMMY');
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $I->sendDelete('/group-funds/delete-product/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listGroupTransferBillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/by-biller', [
            'biller_id' => 'DUMMY'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listGroupTransferNotFound(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countBiller == 0) {
            $loop = 1;
        } else if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                $response = $I->sendPostAsJson('/group-funds/by-biller', [
                    'biller_id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listGroupTransferInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/by-biller', [
            'biller_id' => 'DUMMY1234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'biller_id' => array('The biller id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listGroupTransferInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/by-biller', [
            'biller_id' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'biller_id' => array('The biller id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Group Transfer Funds By Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY', 'DMY-2'],
            'created_by' => 'Tegar'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 0) {
            $loop = 1;
        } else if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($countProduct == 0) {
                    $loop2 = 1;
                } else if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 0) {
                    $loop3 = 1;
                } else if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countData == 0) {
                    $loop4 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Success']);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DUMMY', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DMY-2', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataAccountNotFound(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY', 'DMY-2'],
            'created_by' => 'Tegar'
        ];
        $response = $I->sendPostAsJson('/group-funds/add', $data);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY', 'DMY-2'],
            'created_by' => 'Tegar'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 0) {
            $loop = 1;
        } else if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($countProduct == 0) {
                    $loop2 = 1;
                } else if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 0) {
                    $loop3 = 1;
                } else if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countData == 0) {
                    $loop4 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->sendPost('/group-funds/add', $data);
                $response = $I->sendPostAsJson('/group-funds/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Exists']);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DUMMY', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DMY-2', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataProductExists(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY'],
            'created_by' => 'Tegar'
        ];
        $data_2 = [
            'id' => 'DUMMY_2',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY', 'DMY-2'],
            'created_by' => 'Tegar'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 0) {
            $loop = 1;
        } else if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($countProduct == 0) {
                    $loop2 = 1;
                } else if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 0) {
                    $loop3 = 1;
                } else if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countData == 0) {
                    $loop4 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->sendPost('/group-funds/add', $data);
                $response = $I->sendPostAsJson('/group-funds/add', $data_2);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data Group Transfer Funds Success but Some Product Exists',
                        'result_data' => ['product_exists' => array()]
                    ]);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DUMMY', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DMY-2', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY_2']);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $I->sendDelete('/group-funds/delete/DUMMY_2');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addDataProductNotRegistered(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY', 'DMY-2'],
            'created_by' => 'Tegar'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 0) {
            $loop = 1;
        } else if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($countData == 0) {
                    $loop2 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/add', $data);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data Group Transfer Funds Success but Some Product Not Registered',
                        'result_data' => ['product_not_registered' => array()]
                    ]);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addDataProductCannotProcessed(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY'],
            'created_by' => 'Tegar'
        ];
        $data_2 = [
            'id' => 'DUMMY_2',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY', 'DMY-2', 'DUMMY-3'],
            'created_by' => 'Tegar'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 0) {
            $loop = 1;
        } else if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($countProduct == 0) {
                    $loop2 = 1;
                } else if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 0) {
                    $loop3 = 1;
                } else if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countData == 0) {
                    $loop4 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->sendPost('/group-funds/add', $data);
                $response = $I->sendPostAsJson('/group-funds/add', $data_2);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data Group Transfer Funds Success but Some Product Cannot Processed',
                        'result_data' => ['product_exists' => array(), 'product_not_registered' => array()]
                    ]);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DUMMY', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DMY-2', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY_2']);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $I->sendDelete('/group-funds/delete/DUMMY_2');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DUMMY'],
            'created_by' => 'Tegar'
        ];
        $data_2 = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'account_src' => '0',
            'account_dest' => '0',
            'type' => 0,
            'product' => ['DMY-2'],
            'created_by' => 'Tegar'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        $countData = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY', 'CSC_GTF_DELETED_DT !=' => null]);
        if ($countAccount == 0) {
            $loop = 1;
        } else if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($countProduct == 0) {
                    $loop2 = 1;
                } else if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 0) {
                    $loop3 = 1;
                } else if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countData == 1) {
                    $loop4 = 1;
                } else if ($countData == 0) {
                    $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
                    if ($count == 1) {
                        $I->sendPut('/group-funds/delete/DUMMY', ['deleted_by' => 'Tegar']);
                        $loop4 = 1;
                    } else if ($count == 0) {
                        $I->sendPost('/group-funds/add', $data);
                        $I->sendPut('/group-funds/delete/DUMMY', ['deleted_by' => 'Tegar']);
                        $loop4 = 1;
                    } else {
                        $I->seeResponseCodeIs(422);
                    }
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/add', $data_2);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DUMMY', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    // $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_PRODUCT' => 'DMY-2', 'CSC_PF_GROUP_TRANSFER' => 'DUMMY']);
                    $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert);
                    // $I->sendDelete('/group-funds/delete-product/' . $dataBpInsert_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            12345678901234567890',
            'name' => 'dummy1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890',
            'account_src' => '0123456789012345678901234567890',
            'account_dest' => '0123456789012345678901234567890',
            'type' => 10,
            'product' => ['dummy1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890'],
            'created_by' => 'dummy1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890'
        ];
        $response = $I->sendPostAsJson('/group-funds/add', $data);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'id' => array('The id must not be greater than 50 characters.'),
                    'name' => array('The name must not be greater than 100 characters.'),
                    'account_src' => array('The account src must be between 1 and 20 digits.'),
                    'account_dest' => array('The account dest must be between 1 and 20 digits.'),
                    'type' => array('The type must be 1 digits.'),
                    'product.0' => array('The product.0 must not be greater than 100 characters.'),
                    'created_by' => array('The created by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/add', [
            'id' => 'DUMMY',
            'name' => 'DUMMY',
            'account_src' => 'aa',
            'account_dest' => 'aa',
            'type' => 'aa',
            'product' => ['DUMMY'],
            'created_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'account_src' => array('The account src must be a number.', 'The account src must be between 1 and 20 digits.'),
                    'account_dest' => array('The account dest must be a number.', 'The account dest must be between 1 and 20 digits.'),
                    'type' => array('The type must be a number.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/add', [
            'id' => '',
            'name' => '',
            'account_src' => '',
            'account_dest' => '',
            'type' => null,
            'product' => '',
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'id' => array('The id field is required.'),
                    'name' => array('The name field is required.'),
                    'account_src' => array('The account src field is required.'),
                    'account_dest' => array('The account dest field is required.'),
                    'type' => array('The type field is required.'),
                    'product' => array('The product field is required.'),
                    'created_by' => array('The created by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPostAsJson('/group-funds/get-data', [
                    'id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Group Transfer Funds Success']);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/get-data', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/get-data', [
            'id' => 'DUMMY12345678901234567890123456789012345678901234567890123456789012345678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'id' => array('The id must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/get-data', [
            'id' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'id' => array('The id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'name' => 'dummy-updated',
            'account_src' => 0,
            'account_dest' => 0,
            'type' => 0,
            'modified_by' => 'Tegar'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($countAccount == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                if ($count == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/update/DUMMY', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Success']);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDataAccountNotFound(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'name' => 'dummy-updated',
            'account_src' => 0,
            'account_dest' => 0,
            'type' => 0,
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/update/DUMMY', $data_update);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
                    $I->sendDelete('/account/delete/0');
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function updateDataNotFound(ApiTester $I)
    {
        $data_update = [
            'name' => 'dummy-updated',
            'account_src' => 0,
            'account_dest' => 0,
            'type' => 0,
            'modified_by' => 'Tegar'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($countAccount == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                $response = $I->sendPutAsJson('/group-funds/update/DUMMY', $data_update);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => 'dummy-updated123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            123456789012234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'account_src' => 1234567890123456789012345,
            'account_dest' => 1234567890123456789012345,
            'type' => 12345,
            'modified_by' => 'Tegar123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890'
        ];
        $response_body = [
            'name' => array('The name must not be greater than 100 characters.'),
            'account_src' => array('The account src must be between 1 and 20 digits.'),
            'account_dest' => array('The account dest must be between 1 and 20 digits.'),
            'type' => array('The type must be 1 digits.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidNumeric(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => 'dummy-updated',
            'account_src' => 'abc',
            'account_dest' => 'abc',
            'type' => 'a',
            'modified_by' => 'Tegar'
        ];
        $response_body = [
            'account_src' => array('The account src must be a number.', 'The account src must be between 1 and 20 digits.'),
            'account_dest' => array('The account dest must be a number.', 'The account dest must be between 1 and 20 digits.'),
            'type' => array('The type must be an integer.', 'The type must be 1 digits.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => '',
            'account_src' => null,
            'account_dest' => null,
            'type' => null,
            'modified_by' => ''
        ];
        $response_body = [
            'name' => array('The name field is required.'),
            'account_src' => array('The account src field is required.'),
            'account_dest' => array('The account dest field is required.'),
            'type' => array('The type field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/delete/DUMMY', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Transfer Funds Success']);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/group-funds/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPutAsJson('/group-funds/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function listProductGroupTransferSuccess(ApiTester $I)
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_PF_ID' => '12345',
            'CSC_PF_GROUP_TRANSFER' => 'DUMMY',
            'CSC_PF_PRODUCT' => 'DUMMY'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        if ($countProduct == 1) {
            $I->sendDelete('/product/delete/DUMMY');
            $loop = 1;
        } else if ($countProduct == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countGroupTransfer == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countGroupTransfer == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $I->sendDelete('/group-funds/delete-product/'.$ID);
                    $loop3 = 1;
                } else if ($count == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_PRODUCT_FUNDS', $data);
                $response = $I->sendPostAsJson('/group-funds/list-product', [
                    'group_transfer' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Get List Product-Group Transfer Funds Success',
                        'result_data' => ['per_page' => 50]
                    ]);
                    $I->sendDelete('/product/delete/DUMMY');
                    $ID_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
                    $I->sendDelete('/group-funds/delete-product/'.$ID_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listProductGroupTransfer_GroupTransferNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-product', [
            'group_transfer' => '0',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listProductGroupTransferNotFound(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
                $response = $I->sendPostAsJson('/group-funds/list-product', [
                    'group_transfer' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Product-Group Transfer Funds Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listProductGroupTransferInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-product', [
            'group_transfer' => 'DUMMY1234512345678901234512345678901234567890123456789012345678901234567890',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'group_transfer' => array('The group transfer must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listProductGroupTransferInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-product', [
            'group_transfer' => '',
            'items' => null
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'group_transfer' => array('The group transfer field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormProductGroupTransferSimpleSuccess(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_BP_ID' => '12345',
            'CSC_BP_PRODUCT' => 'DUMMY',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countProduct == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($count == 1) {
                    $I->sendDelete('/biller/delete-product/'.$ID);
                    $loop3 = 1;
                } else if ($count == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data);
                $response = $I->sendPostAsJson('/group-funds/list-add-product/simple', [
                    'biller_id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Add Product-Group Transfer Funds Success']);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/biller/delete-product/12345');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listAddFormProductGroupTransferDetailSuccess(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_BP_ID' => '12345',
            'CSC_BP_PRODUCT' => 'DUMMY',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countProduct == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($count == 1) {
                    $I->sendDelete('/biller/delete-product/'.$ID);
                    $loop3 = 1;
                } else if ($count == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data);
                $response = $I->sendPostAsJson('/group-funds/list-add-product/detail', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Add Product-Group Transfer Funds Success', 
                        'result_data' => ['per_page' => 50]]);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/biller/delete-product/12345');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listAddFormProductGroupTransferSimpleNotFound(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countProduct == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                $response = $I->sendPostAsJson('/group-funds/list-add-product/simple', [
                    'biller_id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Add Product-Group Transfer Funds Not Found']);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/product/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listAddFormProductGroupTransferDetailNotFound(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countProduct == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                $response = $I->sendPostAsJson('/group-funds/list-add-product/detail', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Add Product-Group Transfer Funds Not Found']);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/product/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listAddFormProductGroupTransferSimple_BillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-add-product/simple', [
            'biller_id' => 'DUMMY'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listAddFormProductGroupTransferDetail_BillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-add-product/detail', [
            'biller_id' => 'DUMMY',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listAddFormProductGroupTransferSimpleInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-add-product/simple', [
            'biller_id' => 'DUMMY12345'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'biller_id' => array('The biller id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormProductGroupTransferDetailInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-add-product/detail', [
            'biller_id' => 'DUMMY12345',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'biller_id' => array('The biller id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormProductGroupTransferSimpleInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-add-product/simple', [
            'biller_id' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'biller_id' => array('The biller id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormProductGroupTransferDetailInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/list-add-product/detail', [
            'biller_id' => '',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'biller_id' => array('The biller id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addProductGroupTransferSuccess(ApiTester $I) //
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 5,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DMY-2']);
        $ID_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DMY-2']);
        if ($countProduct == 1) {
            $I->sendDelete('/product/delete/DUMMY');
            $loop = 1;
        } else if ($countProduct == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop2 = 1;
                } else if ($countProduct_2 == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countGroupTransfer == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countGroupTransfer == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $I->sendDelete('/group-funds/delete-product/'.$ID);
                    $loop4 = 1;
                } else if ($count == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                if ($count_2 == 1) {
                    $I->sendDelete('/group-funds/delete-product/'.$ID_2);
                    $loop5 = 1;
                } else if ($count_2 == 0) {
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/add-product', [
                    'group_transfer' => 'DUMMY',
                    'product' => ['DUMMY', 'DMY-2']
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Success']);
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $ID_3 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
                    $ID_4 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DMY-2']);
                    $I->sendDelete('/group-funds/delete-product/'.$ID_3);
                    $I->sendDelete('/group-funds/delete-product/'.$ID_4);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addProductGroupTransfer_GroupTransferNotFound(ApiTester $I)
    {
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        if ($countProduct == 1) {
            $I->sendDelete('/product/delete/DUMMY');
            $loop = 1;
        } else if ($countProduct == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                $response = $I->sendPostAsJson('/group-funds/add-product', [
                    'group_transfer' => '0',
                    'product' => ['DUMMY']
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
                    $I->sendDelete('/product/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addProductGroupTransfer_ProductExists(ApiTester $I) 
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 2,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_2 = [
            'CSC_TD_NAME' => 'DMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_PF_ID' => '1',
            'CSC_PF_GROUP_TRANSFER' => 'DUMMY',
            'CSC_PF_PRODUCT' => 'DUMMY'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DMY-2']);
        $ID_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DMY-2']);
        if ($countProduct == 1) {
            $I->sendDelete('/product/delete/DUMMY');
            $loop = 1;
        } else if ($countProduct == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop2 = 1;
                } else if ($countProduct_2 == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($countGroupTransfer == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countGroupTransfer == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $I->sendDelete('/group-funds/delete-product/'.$ID);
                    $loop4 = 1;
                } else if ($count == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->haveInDatabase('CSCCORE_PRODUCT_FUNDS', $data);
                if ($count_2 == 1) {
                    $I->sendDelete('/group-funds/delete-product/'.$ID_2);
                    $loop5 = 1;
                } else if ($count_2 == 0) {
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $response = $I->sendPostAsJson('/group-funds/add-product', [
                    'group_transfer' => 'DUMMY',
                    'product' => ['DUMMY', 'DMY-2']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Success But Some Product Exists',
                        'result_data' => ['product_exists' => array()]]);
                    $I->sendDelete('/product/delete/DUMMY');
                    $I->sendDelete('/product/delete/DMY-2');
                    $ID_3 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
                    $ID_4 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DMY-2']);
                    $I->sendDelete('/group-funds/delete-product/'.$ID_3);
                    $I->sendDelete('/group-funds/delete-product/'.$ID_4);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addProductGroupTransfer_ProductNotRegistered(ApiTester $I) 
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 2,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($countGroupTransfer == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($countGroupTransfer == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                $response = $I->sendPostAsJson('/group-funds/add-product', [
                    'group_transfer' => 'DUMMY',
                    'product' => ['DUMMY']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Success But Some Product Not Registered',
                        'result_data' => ['product_not_registered' => array()]]);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addProductGroupTransfer_ProductCannotProcessed(ApiTester $I) 
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 2,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_product = [
            'CSC_TD_NAME' => 'DUMMY',
            'CSC_TD_GROUPNAME' => 'DUMMY',
            'CSC_TD_ALIASNAME' => 'DUMMY',
            'CSC_TD_DESC' => 'DUMMY',
            'CSC_TD_TABLE' => 'DUMMY',
            'CSC_TD_CRITERIA' => 'DUMMY',
            'CSC_TD_FINDCRITERIA' => 'DUMMY',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY',
            'CSC_TD_BANK_COLUMN' => 'DUMMY',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY',
            'CSC_TD_DATE_COLUMN' => 'DUMMY',
            'CSC_TD_NREK_COLUMN' => 'DUMMY',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY',
            'CSC_TD_TABLE_ARCH' => 'DUMMY',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY',
            'CSC_TD_TYPE_TRX' => 'DUMMY',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_PF_ID' => '1',
            'CSC_PF_GROUP_TRANSFER' => 'DUMMY',
            'CSC_PF_PRODUCT' => 'DUMMY'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
        if ($countProduct == 1) {
            $I->sendDelete('/product/delete/DUMMY');
            $loop = 1;
        } else if ($countProduct == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countGroupTransfer == 1) {
                    $I->sendDelete('/group-funds/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countGroupTransfer == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $I->sendDelete('/group-funds/delete-product/'.$ID);
                    $loop3 = 1;
                } else if ($count == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_PRODUCT_FUNDS', $data);
                $response = $I->sendPostAsJson('/group-funds/add-product', [
                    'group_transfer' => 'DUMMY',
                    'product' => ['DUMMY' ,'DMY-2']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Success but Some Product Cannot Processed',
                        'result_data' => ['product_exists' => array(), 'product_not_registered' => array()]]);
                    $I->sendDelete('/product/delete/DUMMY');
                    $ID_2 = $I->grabFromDatabase('CSCCORE_PRODUCT_FUNDS', 'CSC_PF_ID', ['CSC_PF_GROUP_TRANSFER' => 'DUMMY', 'CSC_PF_PRODUCT' => 'DUMMY']);
                    $I->sendDelete('/group-funds/delete-product/'.$ID_2);
                    $I->sendDelete('/group-funds/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addProductGroupTransferInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-funds/add-product', [
            'group_transfer' => 'DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
                            1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'product' => ['DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890DUMMY1234567890123456789012
            345678901234567890123456789012345678901234567890123456789012345678901234567890']
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'group_transfer' => array('The group transfer must not be greater than 50 characters.'),
                    'product.0' => array('The product.0 must not be greater than 100 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addProductGrouptransferInvalidMandatory(ApiTester $I) 
    {
        $response = $I->sendPostAsJson('/group-funds/add-product', [
            'group_transfer' => '',
            'product' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'group_transfer' => array('The group transfer field is required.'),
                    'product' => array('The product field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteProductGroupTransferSuccess(ApiTester $I) //
    {
        $data_group_transfer = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 2,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data = [
            'CSC_PF_ID' => '123456',
            'CSC_PF_GROUP_TRANSFER' => 'DUMMY',
            'CSC_PF_PRODUCT' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_PRODUCT_FUNDS', ['CSC_PF_ID' => '123456']);
        $countGroupTransfer = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($countGroupTransfer == 1) {
            $I->sendDelete('/group-funds/delete/DUMMY');
            $loop = 1;
        } else if ($countGroupTransfer == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data_group_transfer);
                if ($count == 1) {
                    $I->sendDelete('/group-funds/delete-product/123456');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_PRODUCT_FUNDS', $data);
                $response = $I->sendDeleteAsJson('/group-funds/delete-product/123456');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product-Group Transfer Funds Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product-Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteProductGroupBillerNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/group-funds/delete-product/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product-Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Data Product-Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GTF_ID' => 'DUMMY',
            'CSC_GTF_SOURCE' => '1234567890',
            'CSC_GTF_DESTINATION' => '1234567890',
            'CSC_GTF_NAME' => 'dummy',
            'CSC_GTF_TRANSFER_TYPE' => 0,
            'CSC_GTF_PRODUCT_COUNT' => 1,
            'CSC_GTF_CREATED_BY' => 'Tegar',
            'CSC_GTF_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_TRANSFER_FUNDS', ['CSC_GTF_ID' => 'DUMMY']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_TRANSFER_FUNDS', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/group-funds/delete/DUMMY');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Group Transfer Funds Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Group Transfer Funds Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/group-funds/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Transfer Funds Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Group Transfer Funds Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function _after(ApiTester $I)
    {
        $I->amBearerAuthenticated('');
        $I->deleteHeader('Content-Type');
        $I->deleteHeader('Accept');
        $I->deleteHeader('x-auth-key');
    }
}
