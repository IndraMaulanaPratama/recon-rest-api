<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class PartnerCest
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
        $response = $I->sendGetAsJson('/partner/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Partner Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/partner/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Partner Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/partner/filter?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Partner Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/partner/trash?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Partner Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Partner Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/partner/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Partner Success']);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPostAsJson('/partner/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Partner Exists']);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy', 'CSC_PARTNER_DELETED_DT !=' => null]);
        if ($countData == 1) {
            $loop2 = 1;
        } else if ($countData == 0) {
            $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/partner/delete/dummy', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/partner/add', $data);
                    $I->sendPut('/partner/delete/dummy', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/partner/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/add', [
            'id' => 'dummy123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
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
                    'id' => array('The id must not be greater than 50 characters.'),
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/add', [
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPostAsJson('/partner/get-data', [
                    'id' => 'dummy'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Partner Success']);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/get-data', [
            'id' => 'dummy-123-456'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/get-data', [
            'id' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/get-data', [
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_update = [
            'name' => 'dummy-updated',
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPutAsJson('/partner/update/dummy', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Partner Success']);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Partner Failed']);
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
        $response = $I->sendPutAsJson('/partner/update/dummy-123-456', $data_update);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890dummy12345789021345678902134678908123
            4567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'modified_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'name' => array('The name must not be greater than 100 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPutAsJson('/partner/update/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'name' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'name' => array('The name field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPutAsJson('/partner/update/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPutAsJson('/partner/delete/dummy', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Partner Success']);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/partner/delete/dummy-123-456', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Partner Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPutAsJson('/partner/delete/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/partner/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/partner/add', $data);
                $response = $I->sendPutAsJson('/partner/delete/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/partner/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function listCIDPartnerSuccess(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $data_cid_partner = [
            'CSC_PC_ID' => '00000',
            'CSC_PC_CID' => '00',
            'CSC_PC_PARTNER' => 'DUMMY'
        ];
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $countCIDPartner = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_ID' => '00000']);
        $dataPcID = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop2 = 1;
                } else if ($countCID == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countPartner == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countCIDPartner == 1) {
                    $I->sendDelete('/partner/delete-cid/'.$dataPcID);
                    $loop4 = 1;
                } else if ($countCIDPartner == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->haveInDatabase('CSCCORE_PARTNER_CID', $data_cid_partner);
                $response = $I->sendPostAsJson('/partner/list-cid', [
                    'partner' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Get List CID-Partner Success',
                        'result_data' => ['per_page' => 50]
                    ]);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $I->sendDelete('/partner/delete-cid/00000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listCIDPartner_PartnerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/list-cid', [
            'partner' => '0',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listCIDPartnerNotFound(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countPartner == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop3 = 1;
                } else if ($countCID == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                $response = $I->sendPostAsJson('/partner/list-cid', [
                    'partner' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data CID-Partner Not Found']);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listCIDPartnerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/list-cid', [
            'partner' => 'DUMMY1234512345678901234567890123451234567890123456789012345123456789012345678901234512345678901234567890
            12345123456789012345678901234512345678901234567890123451234567890123456789012345123456789012345678901234512345678901234567890',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'partner' => array('The partner must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listCIDPartnerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/list-cid', [
            'partner' => '',
            'items' => null
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'partner' => array('The partner field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addCIDPartnerSuccess(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $dataPcID = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $countPartnerCID = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop2 = 1;
                } else if ($countCID == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countPartner == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countPartnerCID == 1) {
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID);
                    $loop4 = 1;
                } else if ($countPartnerCID == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/partner/add-cid', [
                    'partner' => 'DUMMY',
                    'cid' => ['00']
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Success']);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $dataPcID_2 = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addCIDPartner_PartnerNotFound(ApiTester $I)
    {
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop2 = 1;
                } else if ($countCID == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                $response = $I->sendPostAsJson('/partner/add-cid', [
                    'partner' => 'DUMMY',
                    'cid' => ['00']
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addCIDPartner_CIDNotFound(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $dataPcID = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $countPartnerCID = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop2 = 1;
                } else if ($countCID == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countPartner == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countPartnerCID == 1) {
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID);
                    $loop4 = 1;
                } else if ($countPartnerCID == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/partner/add-cid', [
                    'partner' => 'DUMMY',
                    'cid' => ['00', '01']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Success But Some CID Not Registered',
                        'result_data' => ['cid_not_registered' => array()]]);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $dataPcID_2 = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addCIDPartner_CIDExists(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $data_cid_partner = [
            'CSC_PC_ID' => '00000',
            'CSC_PC_CID' => '00',
            'CSC_PC_PARTNER' => 'DUMMY'
        ];
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $countPartnerCID = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop2 = 1;
                } else if ($countCID == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countPartner == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countPartnerCID == 1) {
                    $I->sendDelete('/partner/delete-cid/00000');
                    $loop4 = 1;
                } else if ($countPartnerCID == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->haveInDatabase('CSCCORE_PARTNER_CID', $data_cid_partner);
                $response = $I->sendPostAsJson('/partner/add-cid', [
                    'partner' => 'DUMMY',
                    'cid' => ['00']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data CID-Partner Success But Some CID Exists',
                        'result_data' => ['cid_exists' => array()]
                    ]);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $I->sendDelete('/partner/delete-cid/00000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addCIDPartner_CIDCannotProcessed(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => 'DUMMY',
            'CSC_PROFILE_NAME' => 'DUMMY',
            'CSC_PROFILE_DESC' => 'DUMMY',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $data_cid_partner = [
            'CSC_PC_ID' => '00000',
            'CSC_PC_CID' => '00',
            'CSC_PC_PARTNER' => 'DUMMY'
        ];
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => 'DUMMY']);
        $dataPcID = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $countPartnerCID = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        $I->amConnectedToDatabase('default');
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/DUMMY');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/00');
                    $loop2 = 1;
                } else if ($countCID == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('db_recon');
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countPartner == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countPartnerCID == 1) {
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID);
                    $loop4 = 1;
                } else if ($countPartnerCID == 0) {
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $I->haveInDatabase('CSCCORE_PARTNER_CID', $data_cid_partner);
                $response = $I->sendPostAsJson('/partner/add-cid', [
                    'partner' => 'DUMMY',
                    'cid' => ['00', '01']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Insert Data CID-Partner Success But Some CID Cannot Processed',
                        'result_data' => ['cid_exists' => array(), 'cid_not_registered' => array()]
                    ]);
                    $I->sendDelete('/profile/delete/DUMMY');
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $dataPcID_2 = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addCIDPartnerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/add-cid', [
            'partner' => '000000000000000000000000000000000000000000000000000000000000',
            'cid' => ['0000000000']
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'partner' => array('The partner must not be greater than 50 characters.'),
                    'cid.0' => array('The cid.0 must not be greater than 7 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addCIDPartnerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/add-cid', [
            'partner' => '',
            'cid' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'partner' => array('The partner field is required.'),
                    'cid' => array('The cid field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteCIDPartnerSuccess(ApiTester $I)
    {
        $data = [
            'CSC_PC_ID' => '00000',
            'CSC_PC_CID' => '00',
            'CSC_PC_PARTNER' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_ID' => '00000']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_PARTNER_CID', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/partner/delete-cid/00000');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data CID-Partner Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteCIDPartnerNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/partner/delete-cid/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID-Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Data CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getUnmappingCIDPartner(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/partner/unmapping-cid?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Unmapping Partner-CID Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Unmapping Partner-CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Unmapping Partner-CID Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addUnmappingCIDPartnerSuccess(ApiTester $I)
    {   
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $dataPcID = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $countPartnerCID = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        if ($countCID == 1) {
            $I->sendDelete('/cid/delete/00');
            $loop = 1;
        } else if ($countCID == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countPartner == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countPartnerCID == 1) {
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID);
                    $loop3 = 1;
                } else if ($countPartnerCID == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPostAsJson('/partner/add-unmapping-cid', [
                    'partner' => 'DUMMY',
                    'cid' => '00'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Success']);
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $dataPcID_2 = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addUnmappingCIDPartnerExists(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
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
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        $dataPcID = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $countPartnerCID = $I->grabNumRecords('CSCCORE_PARTNER_CID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        if ($countCID == 1) {
            $I->sendDelete('/cid/delete/00');
            $loop = 1;
        } else if ($countCID == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                if ($countPartner == 1) {
                    $I->sendDelete('/partner/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countPartner == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                if ($countPartnerCID == 1) {
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID);
                    $loop3 = 1;
                } else if ($countPartnerCID == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->sendPost('/partner/add-unmapping-cid', [
                    'partner' => 'DUMMY',
                    'cid' => '00'
                ]);
                $response = $I->sendPostAsJson('/partner/add-unmapping-cid', [
                    'partner' => 'DUMMY',
                    'cid' => '00'
                ]);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Unmapping CID-Partner Exists']);
                    $I->sendDelete('/cid/delete/00');
                    $I->sendDelete('/partner/delete/DUMMY');
                    $dataPcID_2 = $I->grabFromDatabase('CSCCORE_PARTNER_CID', 'CSC_PC_ID', ['CSC_PC_CID' => '00', 'CSC_PC_PARTNER' => 'DUMMY']);
                    $I->sendDelete('/partner/delete-cid/' . $dataPcID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addUnmappingCIDPartner_CIDNotFound(ApiTester $I)
    {
        $data_partner = [
            'CSC_PARTNER_ID' => 'DUMMY',
            'CSC_PARTNER_NAME' => 'DUMMY',
            'CSC_PARTNER_CREATED_BY' => 'Tegar',
            'CSC_PARTNER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countPartner = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'DUMMY']);
        if ($countPartner == 1) {
            $I->sendDelete('/partner/delete/DUMMY');
            $loop = 1;
        } else if ($countPartner == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_PARTNER', $data_partner);
                $response = $I->sendPostAsJson('/partner/add-unmapping-cid', [
                    'partner' => 'DUMMY',
                    'cid' => '000'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
                    $I->sendDelete('/partner/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
        }
    }
    
    public function addUnmappingCIDPartner_PartnerNotFound(ApiTester $I)
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
        $I->amConnectedToDatabase('db_recon');
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '00']);
        if ($countCID == 1) {
            $I->sendDelete('/cid/delete/00');
            $loop = 1;
        } else if ($countCID == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_DOWN_CENTRAL', $data_cid);
                $response = $I->sendPostAsJson('/partner/add-unmapping-cid', [
                    'partner' => 'DUMMY',
                    'cid' => '00'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
                    $I->sendDelete('/cid/delete/00');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addUnmappingCIDPartnerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/add-unmapping-cid', [
            'partner' => '000000000000000000000000000000000000000000000000000000000000',
            'cid' => '0000000000'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'partner' => array('The partner must not be greater than 50 characters.'),
                    'cid' => array('The cid must not be greater than 7 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addUnmappingCIDPartnerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/partner/add-cid', [
            'partner' => '',
            'cid' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'partner' => array('The partner field is required.'),
                    'cid' => array('The cid field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Unmapping CID-Partner Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'dummy',
            'name' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_PARTNER', ['CSC_PARTNER_ID' => 'dummy']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->sendPost('/partner/add', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/partner/delete/dummy');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Partner Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Partner Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/partner/delete/dummy-123-456');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Partner Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Partner Failed']);
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
