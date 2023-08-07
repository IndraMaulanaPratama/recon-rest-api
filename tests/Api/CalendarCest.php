<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class CalendarCest
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
        $response = $I->sendGetAsJson('/calendar/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Calendar Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/calendar/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Calendar Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/calendar/filter?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Calendar Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/calendar/trash?name=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Calendar Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Trash Data Calendar Failed']);
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
        $countData = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/calendar/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Success']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Failed']);
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
        $countData = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/calendar/add', $data);
                $response = $I->sendPostAsJson('/calendar/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Calendar Exists']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Failed']);
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
        $countData = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY', 'CSC_CAL_DELETED_DT !=' => null]);
        if ($countData == 0) {
            $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
            if ($count == 1) {
                $I->sendPut('/calendar/delete/DUMMY', ['deleted_by' => 'Tegar']);
                $loop = 1;
            } else if ($count == 0) {
                $I->sendPost('/calendar/add', $data);
                $I->sendPut('/calendar/delete/DUMMY', ['deleted_by' => 'Tegar']);
                $loop = 1;
            } else {
                $I->seeResponseCodeIs(422);
            }
        } else if ($countData == 1) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/calendar/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/calendar/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Failed']);
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
            'created_by' => 'dummy1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890'
        ];
        $response = $I->sendPostAsJson('/calendar/add', $data);
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/add', [
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPostAsJson('/calendar/get-data', [
                    'id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Success']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data', [
            'id' => 'DUMMY12345678901234567890123456789012345678901234567890123456789012345678901234567890'
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data', [
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_update = [
            'name' => 'dummy-updated',
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPutAsJson('/calendar/update/DUMMY', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Success']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Failed']);
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
        $response = $I->sendPutAsJson('/calendar/update/DUMMY', $data_update);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data_invalid = [
            'name' => 'dummy-updated123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            123456789012234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'modified_by' => 'Tegar123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890'
        ];
        $response_body = [
            'name' => array('The name must not be greater than 100 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $response = $I->sendPutAsJson('/calendar/update/DUMMY', $data_invalid);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => $response_body]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data_invalid = [
            'name' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'name' => array('The name field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $response = $I->sendPutAsJson('/calendar/update/DUMMY', $data_invalid);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => $response_body]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function setDefaultDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPutAsJson('/calendar/set-default/DUMMY');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Set Default Data Calendar Success']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Set Default Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function setDefaultDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/set-default/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found Success']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Set Default Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function setDefaultInvalidLength(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/set-default/123456789012345678901234567890123456789012345');
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => ['0' => 'The id must not be greater than 20 characters.']]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Set Default Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function setDefaultInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/caledanr/set-default/');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Set Default Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getAllDataCopySuccess(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_day = [
            'CSC_CD_ID' => '12345',
            'CSC_CD_CALENDAR' => 'DUMMY',
            'CSC_CD_DATE' => '2023-02-05',
            'CSC_CD_DESC' => 'Libur Nasional'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $countDay = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2023-02-05']);
        $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2023-02-05']);
        if ($countDay == 1) {
            $I->sendDelete('/calendar/delete-day/' . $ID);
            $loop = 1;
        } else if ($countDay == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR_DAYS', $data_day);
                if ($count == 1) {
                    $I->sendDelete('/calendar/delete/DUMMY');
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
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPostAsJson('/calendar/get-data-copy', ['id' => 'DUMMY']);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Copy Calendar Success']);
                    $I->sendDelete('/calendar/delete-day/12345');
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Copy Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getAllDataCopyNotFound(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPostAsJson('/calendar/get-data-copy', ['id' => 'DUMMY']);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Copy Calendar Not Found']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Copy Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function getAllDataCopyCalendarNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data-copy', ['id' => 'DUMMY']);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Copy Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getAllDataCopyInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data-copy', ['id' => 'DUMMY12345678901234567890']);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => ['id' => ['The id must not be greater than 20 characters.']]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Copy Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getAllDataCopyInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data-copy', ['id' => '']);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => ['id' => ['The id field is required.']]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Copy Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function copyDataSuccess(ApiTester $I)
    {
        $data = [
            'id_cal' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar',
            'days' => array(
                [
                    'date' => '2022-12-25',
                    'desc' => 'Hari Raya Natal 2022'
                ],
                [
                    'date' => '2023-01-01',
                    'desc' => 'Tahun Baru 2023'
                ]
            )
        ];
        $countData = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/calendar/copy', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Copy Data Calendar Success']);
                    $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
                    $ID_2 = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2023-01-01']);
                    $I->sendDelete('/calendar/delete-day/' . $ID);
                    $I->sendDelete('/calendar/delete-day/' . $ID_2);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Copy Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function copyDataExists(ApiTester $I)
    {
        $data = [
            'id_cal' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar',
            'days' => array(
                [
                    'date' => '2022-12-25',
                    'desc' => 'Hari Raya Natal 2022'
                ],
                [
                    'date' => '2023-01-01',
                    'desc' => 'Tahun Baru 2023'
                ]
            )
        ];
        $data_cal = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_cal);
                $response = $I->sendPostAsJson('/calendar/copy', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Calendar Exists']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Copy Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function copyDataUnprocessableEntity(ApiTester $I)
    {
        $data = [
            'id_cal' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar',
            'days' => array(
                [
                    'date' => '2022-12-25',
                    'desc' => 'Hari Raya Natal 2022'
                ],
                [
                    'date' => '2023-01-01',
                    'desc' => 'Tahun Baru 2023'
                ]
            )
        ];
        $data_cal = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countData = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_cal);
                $I->sendPut('/calendar/delete/DUMMY', ['deleted_by' => 'Tegar']);
                $response = $I->sendPostAsJson('/calendar/copy', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Copy Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function copyDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id_cal' => 'DUMMY1234567890123456789012345',
            'name' => 'dummy123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
            'created_by' => 'Tegar1`2345678901234567890123456789012345678901234567890123456',
            'days' => array(
                [
                    'date' => '2022-12-251234567890',
                    'desc' => 'Hari Raya Natal 20221234567890123456789012345678901234567890123456789012345'
                ],
                [
                    'date' => '2023-01-011234567890',
                    'desc' => 'Tahun Baru 20231234567890123456789012345678901234567890123456789012345'
                ]
            )
        ];
        $response = $I->sendPostAsJson('/calendar/copy', $data);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id_cal' => array('The id cal must not be greater than 20 characters.'),
                'name' => array('The name must not be greater than 100 characters.'),
                'created_by' => array('The created by must not be greater than 50 characters.'),
                'days.0.date' => array('The days.0.date does not match the format Y-m-d.'),
                'days.0.desc' => array('The days.0.desc must not be greater than 50 characters.'),
                'days.1.date' => array('The days.1.date does not match the format Y-m-d.'),
                'days.1.desc' => array('The days.1.desc must not be greater than 50 characters.')
            ]]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_data' => 'Copy Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function copyDataInvalidDate(ApiTester $I)
    {
        $data = [
            'id_cal' => 'DUMMY',
            'name' => 'dummy',
            'created_by' => 'Tegar',
            'days' => array(
                [
                    'date' => '2022-12-251234567890',
                    'desc' => 'Hari Raya Natal 2022'
                ],
                [
                    'date' => '2023-01-011234567890',
                    'desc' => 'Tahun Baru 2023'
                ]
            )
        ];
        $response = $I->sendPostAsJson('/calendar/copy', $data);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'days.0.date' => array('The days.0.date does not match the format Y-m-d.'),
                'days.1.date' => array('The days.1.date does not match the format Y-m-d.')
            ]]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_data' => 'Copy Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function copyDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id_cal' => '',
            'name' => '',
            'created_by' => '',
            'days' => array(
                [
                    'date' => '',
                    'desc' => ''
                ],
                [
                    'date' => '',
                    'desc' => ''
                ]
            )
        ];
        $response = $I->sendPostAsJson('/calendar/copy', $data);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id_cal' => array('The id cal field is required.'),
                'name' => array('The name field is required.'),
                'created_by' => array('The created by field is required.'),
                'days.0.date' => array('The days.0.date does not match the format Y-m-d.'),
                'days.0.desc' => array('The days.0.desc must be a string.'),
                'days.1.date' => array('The days.1.date does not match the format Y-m-d.'),
                'days.1.desc' => array('The days.1.desc must be a string.')
            ]]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_data' => 'Copy Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPutAsJson('/calendar/delete/DUMMY', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Success']);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I) //error 200
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPutAsJson('/calendar/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I) //error 200
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data);
                $response = $I->sendPutAsJson('/calendar/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function listDayCalendarSuccess(ApiTester $I)
    {
        $data_cal = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $data = [
            'CSC_CD_ID' => '12345',
            'CSC_CD_CALENDAR' => 'DUMMY',
            'CSC_CD_DATE' => '2022-12-25',
            'CSC_CD_DESC' => 'Hari Natal'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        if ($countCalendar == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($countCalendar == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_cal);
                if ($count == 1) {
                    $I->sendDelete('/calendar/delete-day/' . $ID);
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
                $I->haveInDatabase('CSCCORE_CALENDAR_DAYS', $data);
                $response = $I->sendPostAsJson('/calendar/list-day', [
                    'calendar' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Get List Calendar Days Success',
                        'result_data' => ['per_page' => 50]
                    ]);
                    $I->sendDelete('/calendar/delete-day/12345');
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Days Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listDayCalendarNotFound(ApiTester $I)
    {
        $data_cal = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($countCalendar == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($countCalendar == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_cal);
                $response = $I->sendPostAsJson('/calendar/list-day', [
                    'calendar' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Data Calendar Days Not Found'
                    ]);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Days Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listDayCalendar_CalendarNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/list-day', [
            'calendar' => 'DUMMY',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson([
                'result_message' => 'Data Calendar Not Found'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listDayCalendarInvalidLength(ApiTester $I) //error 500
    {
        $response = $I->sendPostAsJson('/calendar/list-day', [
            'calendar' => 'DUMMY1234567890123456789012345',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => ['calendar' => array('The calendar must not be greater than 20 characters.')]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listDayCalendarInvalidMandatory(ApiTester $I) //error 500
    {
        $response = $I->sendPostAsJson('/calendar/list-day', [
            'calendar' => '',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => ['calendar' => array('The calendar field is required.')]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDayCalendarSuccessOrExists(ApiTester $I)
    {
        $data_cal = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        if ($countCalendar == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($countCalendar == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_cal);
                if ($count == 1) {
                    $I->sendDelete('/calendar/delete-day/' . $ID);
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
                $response = $I->sendPostAsJson('/calendar/add-day', [
                    'calendar' => 'DUMMY',
                    'date' => '2022-12-25',
                    'desc' => 'Hari Natal 2022'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Days Success']);
                    $ID_2 = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
                    $I->sendDelete('/calendar/delete-day/' . $ID_2);
                    $I->sendDelete('/calendar/delete/DUMMY');
                } else if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Calendar Days Exists']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Days Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDayCalendar_CalendarNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/add-day', [
            'calendar' => 'DUMMY',
            'date' => '2022-12-25',
            'desc' => 'Hari Natal 2022'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function addDayCalendarInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/add-day', [
            'calendar' => 'DUMMY1234567890123456789012345',
            'date' => '2022-12-2512345',
            'desc' => 'Hari Natal 20221234567890123456789012345678901234567890123456789012345'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'calendar' => array('The calendar must not be greater than 20 characters.'),
                'date' => array('The date does not match the format Y-m-d.'),
                'desc' => array('The desc must not be greater than 50 characters.')
            ]]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDayCalendarInvalidDate(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/add-day', [
            'calendar' => 'DUMMY',
            'date' => '2022-12-2512345',
            'desc' => 'Hari Natal 2022'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'date' => array('The date does not match the format Y-m-d.')
            ]]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDayCalendarInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/add-day', [
            'calendar' => '',
            'date' => '',
            'desc' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'calendar' => array('The calendar field is required.'),
                'date' => array('The date field is required.'),
                'desc' => array('The desc field is required.')
            ]]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDayCalendarSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CD_ID' => '12345',
            'CSC_CD_CALENDAR' => 'DUMMY',
            'CSC_CD_DATE' => '2022-12-25',
            'CSC_CD_DESC' => 'Hari Natal'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete-day/' . $ID);
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR_DAYS', $data);
                $response = $I->sendPostAsJson('/calendar/get-data-day', [
                    'id' => '12345'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Get Data Calendar Days Success'
                    ]);
                    $I->sendDelete('/calendar/delete-day/12345');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Days Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDayCalendarNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data-day', [
            'id' => '12345'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson([
                'result_message' => 'Data Calendar Days Not Found'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDayCalendarInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data-day', [
            'id' => '1234512345678901234567890123456789012345'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => ['id' => array('The id must not be greater than 36 characters.')]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDayCalendarInvaliMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/get-data-day', [
            'id' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => ['id' => array('The id field is required.')]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDayCalendarSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CD_ID' => '12345',
            'CSC_CD_CALENDAR' => 'DUMMY',
            'CSC_CD_DATE' => '2022-12-25',
            'CSC_CD_DESC' => 'Hari Natal'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete-day/' . $ID);
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR_DAYS', $data);
                $response = $I->sendPutAsJson('/calendar/update-day/12345', [
                    'date' => '2023-01-01',
                    'desc' => 'Tahun Baru 2023'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson([
                        'result_message' => 'Update Data Calendar Days Success'
                    ]);
                    $I->sendDelete('/calendar/delete-day/12345');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Days Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDayCalendarNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/update-day/12345', [
            'date' => '2023-01-01',
            'desc' => 'Tahun Baru 2023'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson([
                'result_message' => 'Data Calendar Days Not Found'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDayCalendarInvalidLength(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/update-day/12345', [
            'date' => '2023-01-0112345',
            'desc' => 'Tahun Baru 20231234567890123456789012345678901234567890123456789012345'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'date' => array('The date does not match the format Y-m-d.'),
                    'desc' => array('The desc must not be greater than 50 characters.')
                ]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDayCalendarInvalidDate(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/update-day/12345', [
            'date' => '01-01-2023',
            'desc' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'date' => array('The date does not match the format Y-m-d.')
                ]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDayCalendarInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/calendar/update-day/12345', [
            'date' => '',
            'desc' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson([
                'result_data' => [
                    'date' => array('The date field is required.'),
                    'desc' => array('The desc field is required.')
                ]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteDayCalendarSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CD_ID' => '12345',
            'CSC_CD_CALENDAR' => 'DUMMY',
            'CSC_CD_DATE' => '2022-12-25',
            'CSC_CD_DESC' => 'Hari Natal'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        $ID = $I->grabFromDatabase('CSCCORE_CALENDAR_DAYS', 'CSC_CD_ID', ['CSC_CD_CALENDAR' => 'DUMMY', 'CSC_CD_DATE' => '2022-12-25']);
        if ($count == 1) {
            $I->sendDelete('/calendar/delete-day/' . $ID);
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR_DAYS', $data);
                $response = $I->sendDeleteAsJson('/calendar/delete-day/12345');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Days Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Days Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDayCalendarNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/calendar/delete-day/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Days Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDayCalendarInvalidLength(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/calendar/delete-day/1234567890123456789012345678901234567890');
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => array('The id must not be greater than 36 characters.')]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteDayCalendarInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/calendar/delete-day/');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar Days Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'dummy',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2023-01-11 10:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_CALENDAR', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/calendar/delete/DUMMY');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Calendar Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/calendar/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Calendar Failed']);
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
