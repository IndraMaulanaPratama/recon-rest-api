<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class AccountCest
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
        $response = $I->sendGetAsJson('/account/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Account Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Account Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/account/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Account Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Account Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/account/filter?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Account Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Account Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/account/trash?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Account Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Account Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'number' => 0,
            'bank' => 0,
            'name' => 'dummy',
            'owner' => 'dummy',
            'type' => 0,
            'created_by' => 'Tegar'
        ];
        $data_bank = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countDataBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        $countData = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countDataBank == 0) {
            $loop = 1;
        } else if ($countDataBank == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data_bank);
                if ($countData == 0) {
                    $loop2 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/account/delete/0');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/account/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Success']);
                    $I->sendDelete('/bank/delete/0');
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'number' => 0,
            'bank' => 0,
            'name' => 'dummy',
            'owner' => 'dummy',
            'type' => 0,
            'created_by' => 'Tegar'
        ];
        $data_bank = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countDataBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        $countData = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countDataBank == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($countDataBank == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data_bank);
                if ($countData == 1) {
                    $I->sendDelete('/account/delete/0');
                    $loop2 = 1;
                } else if ($countData == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->sendPost('/account/add', $data);
                $response = $I->sendPostAsJson('/account/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Account Exists']);
                    $I->sendDelete('/bank/delete/0');
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataBankNotFound(ApiTester $I)
    {
        $data = [
            'number' => 0,
            'bank' => 0,
            'name' => 'dummy',
            'owner' => 'dummy',
            'type' => 0,
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countData == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($countData == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/account/add', $data);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'number' => 0,
            'bank' => 0,
            'name' => 'dummy',
            'owner' => 'dummy',
            'type' => 0,
            'created_by' => 'Tegar'
        ];
        $data_bank = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countDataBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        $countData = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0, 'CSC_ACCOUNT_DELETED_DT !=' => null]);
        if ($countDataBank == 1) {
            $I->sendDelete('bank/delete/0');
            $loop = 1;
        } else if ($countDataBank == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data_bank);
                if ($countData == 1) {
                    $loop3 = 1;
                } else if ($countData == 0) {
                    $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/account/delete/0', ['deleted_by' => 'Tegar']);
                    $loop3 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/account/add', $data);
                    $I->sendPut('/account/delete/0', ['deleted_by' => 'Tegar']);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPostAsJson('/account/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/bank/delete/0');
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/add', [
            'number' => 123456789012345678901234567890,
            'bank' => 12345,
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'owner' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'type' => 12345,
            'created_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'number' => array('The number must be between 1 and 20 digits.'),
                    'bank' => array('The bank must be between 1 and 3 digits.'),
                    'name' => array('The name must not be greater than 50 characters.'),
                    'owner' => array('The owner must not be greater than 50 characters.'),
                    'type' => array('The type must be 1 digits.'),
                    'created_by' => array('The created by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/add', [
            'number' => 'NUMBER',
            'bank' => 'BAN',
            'name' => 'dummy',
            'owner' => 'dummy',
            'type' => 'A',
            'created_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'number' => array('The number must be a number.', 'The number must be between 1 and 20 digits.'),
                    'bank' => array('The bank must be an integer.', 'The bank must be between 1 and 3 digits.'),
                    'type' => array('The type must be an integer.', 'The type must be 1 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/add', [
            'number' => null,
            'bank' => null,
            'name' => '',
            'owner' => '',
            'type' => null,
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'number' => array('The number field is required.'),
                    'bank' => array('The bank field is required.'),
                    'name' => array('The name field is required.'),
                    'owner' => array('The owner field is required.'),
                    'type' => array('The type field is required.'),
                    'created_by' => array('The created by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_bank = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countBank == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($countBank == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data_bank);
                if ($count == 1) {
                    $I->sendDelete('/account/delete/0');
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
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPostAsJson('/account/get-data', [
                    'number' => 0
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Account Success']);
                    $I->sendDelete('/bank/delete/0');
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/get-data', [
            'number' => 0
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/get-data', [
            'number' => 1234567890123456789012345
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'number' => array('The number must be between 1 and 20 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/get-data', [
            'number' => 'ABCDEF'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'number' => array('The number must be between 1 and 20 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/account/get-data', [
            'number' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'number' => array('The number field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data_bank = [
            'CSC_BANK_CODE' => 999,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'bank' => 999,
            'name' => 'dummy-updated',
            'owner' => 'dummy-updated',
            'type' => 1,
            'modified_by' => 'Tegar'
        ];
        $countBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 999]);
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($countBank == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($countBank == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data_bank);
                if ($count == 1) {
                    $I->sendDelete('/account/delete/0');
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
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/update/0', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Account Success']);
                    $I->sendDelete('/bank/delete/0');
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDataBankNotFound(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'bank' => 999,
            'name' => 'dummy-updated',
            'owner' => 'dummy-updated',
            'type' => 1,
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/update/0', $data_update);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function updateDataAccountNotFound(ApiTester $I)
    {
        $data_update = [
            'bank' => 1,
            'name' => 'dummy-updated',
            'owner' => 'dummy-updated',
            'type' => 1,
            'modified_by' => 'Tegar'
        ];
        $response = $I->sendPutAsJson('/account/update/0', $data_update);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'bank' => 1234567890,
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890dummy12345789021345678902134678908123
            4567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'owner' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890dummy12345789021345678902134678908123
            4567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'type' => 12345,
            'modified_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'bank' => array('The bank must be between 1 and 3 digits.'),
            'name' => array('The name must not be greater than 50 characters.'),
            'owner' => array('The owner must not be greater than 50 characters.'),
            'type' => array('The type must be 1 digits.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/update/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidNumeric(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'bank' => 'dummy',
            'name' => 'dummy',
            'owner' => 'dummy',
            'type' => 'dummy',
            'modified_by' => 'dummy'
        ];
        $response_body = [
            'bank' => array('The bank must be an integer.', 'The bank must be between 1 and 3 digits.'),
            'type' => array('The type must be an integer.', 'The type must be 1 digits.')
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/update/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'bank' => null,
            'name' => '',
            'owner' => '',
            'type' => null,
            'modified_by' => ''
        ];
        $response_body = [
            'bank' => array('The bank field is required.'),
            'name' => array('The name field is required.'),
            'owner' => array('The owner field is required.'),
            'type' => array('The type field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/update/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/delete/0', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Account Success']);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/account/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Account Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/delete/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $I->sendDelete('/account/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
                $response = $I->sendPutAsJson('/account/delete/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/account/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Account Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'CSC_ACCOUNT_NUMBER' => 0,
            'CSC_ACCOUNT_BANK' => 0,
            'CSC_ACCOUNT_NAME' => 'dummy',
            'CSC_ACCOUNT_OWNER' => 'dummy',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => 0]);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_ACCOUNT', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/account/delete/0');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Account Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Account Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/account/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Account Failed']);
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