<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class BankCest
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
        $response = $I->sendGetAsJson('/bank/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Bank Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Bank Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/bank/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Bank Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Bank Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/bank/filter?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Bank Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Bank Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/bank/trash?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Bank Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Bank Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'code' => 0,
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/bank/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Success']);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'code' => 0,
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/bank/add', $data);
                $response = $I->sendPostAsJson('/bank/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Bank Exists']);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'code' => 0,
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0, 'CSC_BANK_DELETED_DT !=' => null]);
        if ($countData == 1) {
            $loop2 = 1;
        } else if ($countData == 0) {
            $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/bank/delete/0', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/bank/add', $data);
                    $I->sendPut('/bank/delete/0', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/bank/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/bank/add', [
            'code' => 12345,
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'created_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'code' => array('The code must be between 1 and 3 digits.'),
                    'name' => array('The name must not be greater than 50 characters.'),
                    'created_by' => array('The created by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/bank/add', [
            'code' => 'CODE',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'code' => array('The code must be a number.', 'The code must be between 1 and 3 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/bank/add', [
            'code' => null,
            'name' => '',
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'code' => array('The code field is required.'),
                    'name' => array('The name field is required.'),
                    'created_by' => array('The created by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPostAsJson('/bank/get-data', [
                    'code' => 0
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Bank Success']);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/bank/get-data', [
            'code' => 0
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I) //response 200
    {
        $response = $I->sendPostAsJson('/bank/get-data', [
            'code' => 1234567890
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'code' => array('The code must be between 1 and 3 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I) //response 200
    {
        $response = $I->sendPostAsJson('/bank/get-data', [
            'code' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'code' => array('The code field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'name' => 'dummy-updated',
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPutAsJson('/bank/update/0', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Bank Success']);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDataNotFound(ApiTester $I)
    {
        $data_update = [
            'name' => 'dummy-updated',
            'modified_by' => 'Tegar'
        ];
        $response = $I->sendPutAsJson('/bank/update/0', $data_update);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890dummy12345789021345678902134678908123
            4567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'modified_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'name' => array('The name must not be greater than 50 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPutAsJson('/bank/update/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'name' => array('The name field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPutAsJson('/bank/update/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPutAsJson('/bank/delete/0', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Bank Success']);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/bank/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Bank Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I) //response 200
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPutAsJson('/bank/delete/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I) //response 200
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $I->sendDelete('/bank/delete/0');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BANK', $data);
                $response = $I->sendPutAsJson('/bank/delete/0', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/bank/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BANK_CODE' => 0,
            'CSC_BANK_NAME' => 'dummy',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => 0]);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_BANK', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/bank/delete/0');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Bank Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Bank Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/bank/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Bank Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Bank Failed']);
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