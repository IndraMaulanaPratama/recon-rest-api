<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class ExcludePartnerCest
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
        $response = $I->sendGetAsJson('/exclude-partner/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Exclude Partner Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Exclude Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/exclude-partner/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Exclude Partner Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Exclude Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/exclude-partner/filter?cid=&cid_name=&product=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Exclude Partner Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Exclude Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data_cid = [
            'CSC_DC_ID' => '00',
            'CSC_DC_PROFILE' => 'DUMMY',
            'CSC_DC_NAME' => 'DUMMY',
            'CSC_DC_ADDRESS' => 'DUMMY',
            'CSC_DC_PHONE' => '08123456789',
            'CSC_DC_PIC_NAME' => 'DUMMY',
            'CSC_DC_PIC_PHONE' => '080987654321',
            'CSC_DC_TYPE' => 0,
            'CSC_DC_FUND_TYPE' => 0,
            'CSC_DC_TERMINAL_TYPE' => '6010',
            'CSC_DC_REGISTERED' => '2022-12-01 07:00:00',
            'CSC_DC_ISBLOCKED' => 0,
            'CSC_DC_MINIMAL_DEPOSIT' => 1000000,
            'CSC_DC_SHORT_ID' => 'VSI',
            'CSC_DC_COUNTER_CODE' => '0',
            'CSC_DC_A_ID' => 'ID VSI',
            'CSC_DC_ALIAS_NAME' => 'ALIAS ID',
            'CSC_DC_CREATED_BY' => 'Tegar',
            'CSC_DC_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_cid_2 = [
            'CSC_DC_ID' => '01',
            'CSC_DC_PROFILE' => 'DUMMY',
            'CSC_DC_NAME' => 'DUMMY',
            'CSC_DC_ADDRESS' => 'DUMMY',
            'CSC_DC_PHONE' => '08123456789',
            'CSC_DC_PIC_NAME' => 'DUMMY',
            'CSC_DC_PIC_PHONE' => '080987654321',
            'CSC_DC_TYPE' => 0,
            'CSC_DC_FUND_TYPE' => 0,
            'CSC_DC_TERMINAL_TYPE' => '6010',
            'CSC_DC_REGISTERED' => '2022-12-01 07:00:00',
            'CSC_DC_ISBLOCKED' => 0,
            'CSC_DC_MINIMAL_DEPOSIT' => 1000000,
            'CSC_DC_SHORT_ID' => 'VSI',
            'CSC_DC_COUNTER_CODE' => '0',
            'CSC_DC_A_ID' => 'ID VSI',
            'CSC_DC_ALIAS_NAME' => 'ALIAS ID',
            'CSC_DC_CREATED_BY' => 'Tegar',
            'CSC_DC_CREATED_DT' => '2022-12-01 07:00:00'
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
            'cid' => ['00', '01'],
            'product' => ['DUMMY', 'DMY-2']
        ];
        $countDataProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countDataProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DMY-2']);
        $count = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DMY-2']);
        $ID_2 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DMY-2']);
        $count_3 = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '01', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $ID_3 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '01', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $count_4 = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '01', 'CSC_EP_PRODUCT' => 'DMY-2']);
        $ID_4 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '01', 'CSC_EP_PRODUCT' => 'DMY-2']);
        $I->amConnectedToDatabase('db_recon');
        $countDataCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $countDataCID_2 = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '01']);
        if ($countDataCID == 0) {
            $loop = 1;
        } else if ($countDataCID == 1) {
            $I->sendDelete('/cid/delete/00');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countDataCID_2 == 0) {
                    $loop2 = 1;
                } else if ($countDataCID_2 == 1) {
                    $I->sendDelete('/cid/delete/01');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid_2);
                if ($countDataProduct == 0) {
                    $loop3 = 1;
                } else if ($countDataProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($countDataProduct_2 == 0) {
                    $loop4 = 1;
                } else if ($countDataProduct_2 == 1) {
                    $I->sendDelete('/product/delete/DMY-2');
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                if ($count == 0) {
                    $loop5 = 1;
                } else if ($count == 1) {
                    $I->sendDelete('/exclude-partner/delete/' . $ID);
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                if ($count_2 == 0) {
                    $loop6 = 1;
                } else if ($count_2 == 1) {
                    $I->sendDelete('/exclude-partner/delete/' . $ID_2);
                    $loop6 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop6) {
            case 1:
                if ($count_3 == 0) {
                    $loop7 = 1;
                } else if ($count_3 == 1) {
                    $I->sendDelete('/exclude-partner/delete/' . $ID_3);
                    $loop7 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop7) {
            case 1:
                if ($count_4 == 0) {
                    $loop8 = 1;
                } else if ($count_4 == 1) {
                    $I->sendDelete('/exclude-partner/delete/' . $ID_4);
                    $loop8 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop8) {
            case 1:
                $response = $I->sendPostAsJson('/exclude-partner/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Success']);
                    $ID_5 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
                    $ID_6 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DMY-2']);
                    $ID_7 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '01', 'CSC_EP_PRODUCT' => 'DUMMY']);
                    $ID_8 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '01', 'CSC_EP_PRODUCT' => 'DMY-2']);
                    $I->sendDelete('/exclude-partner/delete/' . $ID_5);
                    $I->sendDelete('/exclude-partner/delete/' . $ID_6);
                    $I->sendDelete('/exclude-partner/delete/' . $ID_7);
                    $I->sendDelete('/exclude-partner/delete/' . $ID_8);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data_cid = [
            'CSC_DC_ID' => '00',
            'CSC_DC_PROFILE' => 'DUMMY',
            'CSC_DC_NAME' => 'DUMMY',
            'CSC_DC_ADDRESS' => 'DUMMY',
            'CSC_DC_PHONE' => '08123456789',
            'CSC_DC_PIC_NAME' => 'DUMMY',
            'CSC_DC_PIC_PHONE' => '080987654321',
            'CSC_DC_TYPE' => 0,
            'CSC_DC_FUND_TYPE' => 0,
            'CSC_DC_TERMINAL_TYPE' => '6010',
            'CSC_DC_REGISTERED' => '2022-12-01 07:00:00',
            'CSC_DC_ISBLOCKED' => 0,
            'CSC_DC_MINIMAL_DEPOSIT' => 1000000,
            'CSC_DC_SHORT_ID' => 'VSI',
            'CSC_DC_COUNTER_CODE' => '0',
            'CSC_DC_A_ID' => 'ID VSI',
            'CSC_DC_ALIAS_NAME' => 'ALIAS ID',
            'CSC_DC_CREATED_BY' => 'Tegar',
            'CSC_DC_CREATED_DT' => '2022-12-01 07:00:00'
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
            'cid' => ['00'],
            'product' => ['DUMMY']
        ];
        $countDataProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countDataCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        if ($countDataCID == 0) {
            $loop = 1;
        } else if ($countDataCID == 1) {
            $I->sendDelete('/cid/delete/00');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countDataProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countDataProduct == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($count == 1) {
                    $I->sendDelete('/exclude-partner/delete/' . $ID);
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
                $I->sendPost('/exclude-partner/add', $data);
                $response = $I->sendPostAsJson('/exclude-partner/add', $data);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data Exclude Partner Success But Some Data Cannot Processed',
                        'result_data' => ['data_exists' => array()]
                    ]);
                    $I->sendDelete('/exclude-partner/delete/12345');
                    $ID_2 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
                    $I->sendDelete('/exclude-partner/delete/' . $ID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addDataNotRegistered(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/exclude-partner/add', ['cid' => ['00'], 'product' => ['DUMMY']]);
        if ($response['result_code'] == 202) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(202);
            $I->seeResponseContainsJson([
                'result_message' => 'Insert Data Exclude Partner Success But Some Data Cannot Processed',
                'result_data' => ['cid_not_registered' => array(), 'product_not_registered' => array()]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIs(202);
        }
    }

    public function addDataCannotProcessed(ApiTester $I)
    {
        $data_cid = [
            'CSC_DC_ID' => '00',
            'CSC_DC_PROFILE' => 'DUMMY',
            'CSC_DC_NAME' => 'DUMMY',
            'CSC_DC_ADDRESS' => 'DUMMY',
            'CSC_DC_PHONE' => '08123456789',
            'CSC_DC_PIC_NAME' => 'DUMMY',
            'CSC_DC_PIC_PHONE' => '080987654321',
            'CSC_DC_TYPE' => 0,
            'CSC_DC_FUND_TYPE' => 0,
            'CSC_DC_TERMINAL_TYPE' => '6010',
            'CSC_DC_REGISTERED' => '2022-12-01 07:00:00',
            'CSC_DC_ISBLOCKED' => 0,
            'CSC_DC_MINIMAL_DEPOSIT' => 1000000,
            'CSC_DC_SHORT_ID' => 'VSI',
            'CSC_DC_COUNTER_CODE' => '0',
            'CSC_DC_A_ID' => 'ID VSI',
            'CSC_DC_ALIAS_NAME' => 'ALIAS ID',
            'CSC_DC_CREATED_BY' => 'Tegar',
            'CSC_DC_CREATED_DT' => '2022-12-01 07:00:00'
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
            'cid' => ['00'],
            'product' => ['DUMMY']
        ];
        $data_2 = [
            'cid' => ['00', '01'],
            'product' => ['DUMMY', 'DMY-2']
        ];
        $countDataProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countDataCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        if ($countDataCID == 0) {
            $loop = 1;
        } else if ($countDataCID == 1) {
            $I->sendDelete('/cid/delete/00');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countDataProduct == 1) {
                    $I->sendDelete('/product/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countDataProduct == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
                if ($count == 1) {
                    $I->sendDelete('/exclude-partner/delete/' . $ID);
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
                $I->sendPost('/exclude-partner/add', $data);
                $response = $I->sendPostAsJson('/exclude-partner/add', $data_2);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data Exclude Partner Success But Some Data Cannot Processed',
                        'result_data' => ['data_exists' => array(), 'cid_not_registered' => array(), 'product_not_registered' => array()]
                    ]);
                    $I->sendDelete('/exclude-partner/delete/12345');
                    $ID_2 = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
                    $I->sendDelete('/exclude-partner/delete/' . $ID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/exclude-partner/add', [
            'cid' => ['1234567890'],
            'product' => ['dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            dummy1234578902134567890213467890812345678901234678901234678901234678901234678901234567890312456']
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'cid.0' => array('The cid.0 must not be greater than 7 characters.'),
                    'product.0' => array('The product.0 must not be greater than 100 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/exclude-partner/add', [
            'cid' => '',
            'product' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'cid' => array('The cid field is required.'),
                    'product' => array('The product field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_EP_ID' => '12345',
            'CSC_EP_CID' => '00',
            'CSC_EP_PRODUCT' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_EXCLUDE_PARTNER', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_EXCLUDE_PARTNER', 'CSC_EP_ID', ['CSC_EP_CID' => '00', 'CSC_EP_PRODUCT' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/exclude-partner/delete/' . $ID);
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_EXCLUDE_PARTNER', $data);
                $response = $I->sendDeleteAsJson('/exclude-partner/delete/12345');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Exclude Partner Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Exclude Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/exclude-partner/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Exclude Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/exclude-partner/delete/123456789012345678901234567890123456789012345');
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => ['0' => 'The id must not be greater than 36 characters.']]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Exclude Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/exclude-partner/delete/');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Exclude Partner Failed']);
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
