<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class GroupBillerCest
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
        $response = $I->sendGetAsJson('/group-biller/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Group Biller Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/group-biller/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Group Biller Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/group-biller/filter?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Group Biller Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/group-biller/trash?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Group Biller Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/group-biller/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller Success']);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/group-biller/add', $data);
                $response = $I->sendPostAsJson('/group-biller/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Exists']);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY', 'CSC_GB_DELETED_DT !=' => null]);
        if ($countData == 1) {
            $loop2 = 1;
        } else if ($countData == 0) {
            $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/group-biller/delete/DUMMY', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/group-biller/add', $data);
                    $I->sendPut('/group-biller/delete/DUMMY', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/group-biller/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/add', [
            'id' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZI',
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
                    'id' => array('The id must not be greater than 20 characters.'),
                    'name' => array('The name must not be greater than 100 characters.'),
                    'created_by' => array('The created by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/add', [
            'id' => '',
            'name' => '',
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'id' => array('The id field is required.'),
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPostAsJson('/group-biller/get-data', [
                    'id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Group Biller Success']);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/get-data', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I) //response 200
    {
        $response = $I->sendPostAsJson('/group-biller/get-data', [
            'id' => 'DUMMY12345678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'id' => array('The id must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I) //response 200
    {
        $response = $I->sendPostAsJson('/group-biller/get-data', [
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'name' => 'dummy-updated',
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPutAsJson('/group-biller/update/DUMMY', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Biller Success']);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Biller Failed']);
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
        $response = $I->sendPutAsJson('/group-biller/update/DUMMY', $data_update);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890dummy12345789021345678902134678908123
            4567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            4567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'modified_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'name' => array('The name must not be greater than 100 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPutAsJson('/group-biller/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'name' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'name' => array('The name field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPutAsJson('/group-biller/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPutAsJson('/group-biller/delete/DUMMY', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller Success']);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/group-biller/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I) //response 200
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPutAsJson('/group-biller/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I) //response 200
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPutAsJson('/group-biller/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function listBillerGroupBillerSuccess(ApiTester $I) 
    {
        $data_group_biller = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'dummy',
            'CSC_BILLER_NAME' => 'dummy',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_BC_ID' => '12345',
            'CSC_BC_GROUP_BILLER' => 'DUMMY',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countGroupBiller = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
        $ID = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
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
                if ($countGroupBiller == 1) {
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countGroupBiller == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data_group_biller);
                if ($count == 1) {
                    $I->sendDelete('/group-biller/delete-biller/'.$ID);
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
                $I->haveInDatabase('CSCCORE_BILLER_COLLECTION', $data);
                $response = $I->sendPostAsJson('/group-biller/list-biller', [
                    'group_biller' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Group Biller-Biller Success', 
                        'result_data' => ['per_page' => 50]]);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $ID_2 = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
                    $I->sendDelete('/group-biller/delete-biller/'.$ID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listBillerGroupBiller_GroupBillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/list-biller', [
            'group_biller' => '0',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listBillerGroupBillerNotFound(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
                $response = $I->sendPostAsJson('/group-biller/list-biller', [
                    'group_biller' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Biller-Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listBillerGroupBillerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/list-biller', [
            'group_biller' => 'DUMMY12345123456789012345',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'group_biller' => array('The group biller must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listBillerGroupBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/list-biller', [
            'group_biller' => '',
            'items' => null
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'group_biller' => array('The group biller field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormProductBillerSimpleSuccess(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/list-add-biller/simple', [
            'items' => '50'
        ]);
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get List Add Group Biller-Biller Success']);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listAddFormProductBillerDetailSuccess(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/group-biller/list-add-biller/detail', [
            'items' => '50'
        ]);
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get List Add Group Biller-Biller Success']);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addBillerGroupBillerSuccess(ApiTester $I)
    {
        $data_group_biller = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_biller = [
            'CSC_BILLER_ID' => 'DMY-1',
            'CSC_BILLER_GROUP_PRODUCT' => 'dummy',
            'CSC_BILLER_NAME' => 'dummy',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_biller_2 = [
            'CSC_BILLER_ID' => 'DMY-2',
            'CSC_BILLER_GROUP_PRODUCT' => 'dummy-2',
            'CSC_BILLER_NAME' => 'dummy',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countGroupBiller = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DMY-1']);
        $countBiller_2 = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DMY-2']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-1']);
        $count_2 = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
        $dataBcID = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-1']);
        $dataBcID_2 = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DMY-1');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countBiller_2 == 1) {
                    $I->sendDelete('/biller/delete/DMY-2');
                    $loop2 = 1;
                } else if ($countBiller_2 == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller_2);
                if ($countGroupBiller == 1) {
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countGroupBiller == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data_group_biller);
                if ($count == 1) {
                    $I->sendDelete('/group-biller/delete-biller/'.$dataBcID);
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
                    $I->sendDelete('/group-biller/delete-biller/'.$dataBcID_2);
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
                $response = $I->sendPostAsJson('/group-biller/add-biller', [
                    'group_biller' => 'DUMMY',
                    'biller' => ['DMY-1', 'DMY-2']
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Success']);
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-1']);
                    $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
                    $I->sendDelete('/biller/delete/DMY-1');
                    $I->sendDelete('/biller/delete/DMY-2');
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $I->sendDelete('/group-biller/delete-biller/'.$dataBpInsert);
                    $I->sendDelete('/group-biller/delete-biller/'.$dataBpInsert_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addBillerGroupBiller_GroupBillerNotFound(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DMY-1',
            'CSC_BILLER_GROUP_PRODUCT' => 'dummy',
            'CSC_BILLER_NAME' => 'dummy',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ]; 
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DMY-1']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DMY-1');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                $response = $I->sendPostAsJson('/group-biller/add-biller', [
                    'group_biller' => '0',
                    'biller' => ['DMY-1']
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
                    $I->sendDelete('/biller/delete/DMY-1');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addBillerGroupBiller_BillerExists(ApiTester $I) //error message and data
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_group_biller = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_biller_collection = [
            'CSC_BC_ID' => 'DUMMY',
            'CSC_BC_GROUP_BILLER' => 'DUMMY',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countGroupBiller = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_ID' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
        $dataBcID = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countGroupBiller == 1) {
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countGroupBiller == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data_group_biller);
                if ($count == 1) {
                    $I->sendDelete('/group-biller/delete-biller/DUMMY');
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
                $I->haveInDatabase('CSCCORE_BILLER_COLLECTION', $data_biller_collection);
                if ($count_2 == 1) {
                    $I->sendDelete('/group-biller/delete-biller/'.$dataBcID);
                    $loop4 = 1;
                } else if ($count_2 == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/group-biller/add-biller', [
                    'group_biller' => 'DUMMY',
                    'biller' => ['DUMMY']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Success but Some Biller Exists',
                        'result_data' => ['biller_exists' => array()]]);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $I->sendDelete('/group-biller/delete-biller/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addBillerGroupBiller_BillerNotRegistered(ApiTester $I) //error message and data
    {
        $data_group_biller = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countGroupBiller = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($countGroupBiller == 1) {
            $I->sendDelete('/group-biller/delete/DUMMY');
            $loop = 1;
        } else if ($countGroupBiller == 0) {
            $loop = 1;
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data_group_biller);
                $response = $I->sendPostAsJson('/group-biller/add-biller', [
                    'group_biller' => 'DUMMY',
                    'biller' => ['DUMMY']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Success but Some Biller Not Registered',
                        'result_data' => ['biller_not_registered' => array()]]);
                    $I->sendDelete('/group-biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addBillerGroupBiller_BillerCannotProcessed(ApiTester $I) //error message
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_group_biller = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_biller_collection = [
            'CSC_BC_ID' => 'DUMMY',
            'CSC_BC_GROUP_BILLER' => 'DUMMY',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countGroupBiller = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
        $dataBcID = $I->grabFromDatabase('CSCCORE_BILLER_COLLECTION', 'CSC_BC_ID', ['CSC_BC_GROUP_BILLER' => 'DUMMY', 'CSC_BC_BILLER' => 'DMY-2']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($countGroupBiller == 1) {
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countGroupBiller == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data_group_biller);
                if ($count == 1) {
                    $I->sendDelete('/group-biller/delete-biller/DUMMY');
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
                $I->haveInDatabase('CSCCORE_BILLER_COLLECTION', $data_biller_collection);
                if ($count_2 == 1) {
                    $I->sendDelete('/group-biller/delete-biller/'.$dataBcID);
                    $loop4 = 1;
                } else if ($count_2 == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/group-biller/add-biller', [
                    'group_biller' => 'DUMMY',
                    'biller' => ['DUMMY', 'DMY-2']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Success but Some Biller Cannot Processed',
                        'result_data' => ['biller_exists' => array(), 'biller_not_registered' => array()]]);
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->sendDelete('/group-biller/delete/DUMMY');
                    $I->sendDelete('/group-biller/delete-biller/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addBillerGroupBillerInvalidLength(ApiTester $I) //error property not found
    {
        $response = $I->sendPostAsJson('/group-biller/add-biller', [
            'group_biller' => 'DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
                            1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'biller' => ['DUMMY12345']
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'group_biller' => array('The group biller must not be greater than 20 characters.'),
                'biller.0' => array('The biller.0 must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addBillerGroupBillerInvalidMandatory(ApiTester $I) //error property not found
    {
        $response = $I->sendPostAsJson('/group-biller/add-biller', [
            'group_biller' => '',
            'biller' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'group_biller' => array('The group biller field is required.'),
                'biller' => array('The biller field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteBillerGroupBillerSuccess(ApiTester $I) //error message
    {
        $data = [
            'CSC_BC_ID' => '123456',
            'CSC_BC_GROUP_BILLER' => 'DUMMY',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_BILLER_COLLECTION', ['CSC_BC_ID' => '123456']);
        if ($count == 1) {
            $I->sendDelete('/group-biller/delete-biller/123456');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }
        
        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_COLLECTION', $data);
                $response = $I->sendDeleteAsJson('/group-biller/delete-biller/123456');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller-Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteBillerGroupBillerNotFound(ApiTester $I) //error message
    {
        $response = $I->sendDeleteAsJson('/group-biller/delete-biller/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Data Group Biller-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'CSC_GB_ID' => 'DUMMY',
            'CSC_GB_NAME' => 'dummy',
            'CSC_GB_CREATED_BY' => 'Tegar',
            'CSC_GB_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_GROUP_BILLER', ['CSC_GB_ID' => 'DUMMY']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_BILLER', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/group-biller/delete/DUMMY');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Group Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Group Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/group-biller/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Group Biller Failed']);
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
