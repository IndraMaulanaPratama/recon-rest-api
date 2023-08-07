<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class DownCentralCest
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
        $response = $I->sendGetAsJson('/cid/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List CID Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data CID Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/cid/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List CID Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data CID Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/cid/filter?id=&name=&profile=&type=1&fund_type=&terminal_type=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data CID Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data CID Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/cid/trash?id=&name=&profile=&type=1&fund_type=&terminal_type=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Trash CID Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Trash CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Filter Data CID Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => '0',
            'CSC_PROFILE_NAME' => 'PROFILE-0',
            'CSC_PROFILE_DESC' => 'DESC PROFILE-0',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 10:00:00',
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $countData = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 0) {
            $loop = 1;
        } else if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countData == 0) {
                    $loop2 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/cid/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Success']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => '0',
            'CSC_PROFILE_NAME' => 'PROFILE-0',
            'CSC_PROFILE_DESC' => 'DESC PROFILE-0',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 10:00:00',
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $countData = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countData == 1) {
                    $I->sendDelete('/cid/delete/0000000');
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
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPostAsJson('/cid/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data CID Exists']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => '0',
            'CSC_PROFILE_NAME' => 'PROFILE-0',
            'CSC_PROFILE_DESC' => 'DESC PROFILE-0',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 10:00:00',
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $countData = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000', 'CSC_DC_DELETED_DT !=' => null]);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($countData == 1) {
                    $loop3 = 1;
                } else if ($countData == 0) {
                    $I->amConnectedToDatabase('db_recon');
                    $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/cid/delete/0000000', ['deleted_by' => 'Tegar']);
                    $loop3 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/cid/add', $data);
                    $I->sendPut('/cid/delete/0000000', ['deleted_by' => 'Tegar']);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPostAsJson('/cid/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/add', [
            'id' => '000000000',
            'profile' => '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'name' => 'dummy0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'address' => 'dummy0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'phone' => '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'pic_name' => 'dummy000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'pic_phone' => '0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'type' => 000,
            'fund_type' => 00,
            'terminal_type' => '000000',
            'minimal_deposit' => 0000000000000000,
            'short_id' => 'DMY00',
            'counter_code'=> '00',
            'alias_id'=> '00000000000000000000000000',
            'alias_name' => '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'created_by' => 'Tegar0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id must not be greater than 7 characters.'),
                'profile' => array('The profile must not be greater than 50 characters.'),
                'name' => array('The name must not be greater than 100 characters.'),
                'address' => array('The address must not be greater than 255 characters.'),
                'phone' => array('The phone must not be greater than 50 characters.'),
                'pic_name' => array('The pic name must not be greater than 100 characters.'),
                'pic_phone' => array('The pic phone must not be greater than 100 characters.'),
                'terminal_type' => array('The terminal type must not be greater than 4 characters.'),
                'short_id' => array('The short id must not be greater than 3 characters.'),
                'counter_code' => array('The counter code must not be greater than 1 characters.'),
                'alias_id' => array('The alias id must not be greater than 16 characters.'),
                'alias_name' => array('The alias name must not be greater than 255 characters.'),
                'created_by' => array('The created by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/add', [
            'id' => 'dummy',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 'dummy',
            'fund_type' => 'dummy',
            'terminal_type' => '0000',
            'minimal_deposit' => 'dummy',
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'type' => array('The type must be an integer.', 'The type must be between 1 and 2 digits.'),
                'fund_type' => array('The fund type must be an integer.', 'The fund type must be between 1 and 1 digits.'),
                'minimal_deposit' => array('The minimal deposit must be an integer.', 'The minimal deposit must be between 1 and 10 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/add', [
            'id' => '',
            'profile' => '',
            'name' => '',
            'address' => '',
            'phone' => '',
            'pic_name' => '',
            'pic_phone' => '',
            'type' => null,
            'fund_type' => null,
            'terminal_type' => '',
            'minimal_deposit' => null,
            'short_id' => '',
            'counter_code'=> '',
            'alias_id'=> '',
            'alias_name' => '',
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id field is required.'),
                'profile' => array('The profile field is required.'),
                'created_by' => array('The created by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'CSC_PROFILE_ID' => '0',
            'CSC_PROFILE_NAME' => 'PROFILE-0',
            'CSC_PROFILE_DESC' => 'DESC PROFILE-0',
            'CSC_PROFILE_DEFAULT' => 1,
            'CSC_PROFILE_CREATED_BY' => 'Tegar',
            'CSC_PROFILE_CREATED_DT' => '2022-12-01 10:00:00',
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->amConnectedToDatabase('default');
                $I->haveInDatabase('CSCCORE_PROFILE_FEE', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
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
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPostAsJson('/cid/get-data', [
                    'id' => '0000000'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data CID Success']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/get-data', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/get-data', [
            'id' => '0000000000'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id must not be greater than 7 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/get-data', [
            'id' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_update = [
            'profile' => '0',
            'name' => 'dummy-update',
            'address' => 'dummy-update',
            'phone' => '1111111111111',
            'pic_name' => 'dummy-update',
            'pic_phone' => '222222222222',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'UPD',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'modified_by' => 'Tegar'
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update/0000000', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data CID Success']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/cid/update/0', ['modified_by' => 'Tegar', 'profile' => '0']);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'profile' => '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'name' => 'dummy0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'address' => 'dummy0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'phone' => '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'pic_name' => 'dummy000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'pic_phone' => '0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'type' => 000,
            'fund_type' => 00,
            'terminal_type' => '000000',
            'minimal_deposit' => 0000000000000000,
            'short_id' => 'DMY00',
            'counter_code'=> '00',
            'alias_id'=> '00000000000000000000000000',
            'alias_name' => '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'modified_by' => 'Tegar0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'
        ];
        $response_body = [
            'profile' => array('The profile must not be greater than 50 characters.'),
            'name' => array('The name must not be greater than 100 characters.'),
            'address' => array('The address must not be greater than 255 characters.'),
            'phone' => array('The phone must not be greater than 50 characters.'),
            'pic_name' => array('The pic name must not be greater than 100 characters.'),
            'pic_phone' => array('The pic phone must not be greater than 100 characters.'),
            'terminal_type' => array('The terminal type must not be greater than 4 characters.'),
            'short_id' => array('The short id must not be greater than 3 characters.'),
            'counter_code' => array('The counter code must not be greater than 1 characters.'),
            'alias_id' => array('The alias id must not be greater than 16 characters.'),
            'alias_name' => array('The alias name must not be greater than 255 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidNumeric(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 'dummy',
            'fund_type' => 'dummy',
            'terminal_type' => '0000',
            'minimal_deposit' => 'dummy',
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'modified_by' => 'Tegar'
        ];
        $response_body = [
            'type' => array('The type must be an integer.', 'The type must be between 1 and 2 digits.'),
            'fund_type' => array('The fund type must be an integer.', 'The fund type must be 1 digits.'),
            'minimal_deposit' => array('The minimal deposit must be an integer.', 'The minimal deposit must be between 1 and 10 digits.')
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $I->sendPost('/cid/add', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'profile' => '',
            'name' => '',
            'address' => '',
            'phone' => '',
            'pic_name' => '',
            'pic_phone' => '',
            'type' => null,
            'fund_type' => null,
            'terminal_type' => '',
            'minimal_deposit' => null,
            'short_id' => '',
            'counter_code'=> '',
            'alias_id'=> '',
            'alias_name' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'profile' => array('The profile field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/delete/0000000', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data CID Success']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/cid/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data CID Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            1234567890312456789013245678901234567890123467890213456789031246789021346789021346789012345678901'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/delete/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $I->sendDelete('/cid/delete/0000000');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/delete/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->sendPost('/cid/add', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/cid/delete/0000000');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete CID Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/cid/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete CID Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getProfileCIDSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'id' => '0',
            'name' => 'Profile-0',
            'desc' => 'Deskripsi Profile-0',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/profile/add', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
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
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPostAsJson('/cid/data-profile', [
                    'cid' => '0000000'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Profile-CID Success']);
                    $I->sendDelete('/cid/delete/0000000');
                    $I->sendDelete('/profile/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Profile-CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getProfileCIDNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/data-profile', [
            'cid' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Profile-CID Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getProfileCIDInvalidLength(ApiTester $I) 
    {
        $response = $I->sendPostAsJson('/cid/data-profile', [
            'cid' => '00000000000'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'cid' => array('The cid must not be greater than 7 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Profile-CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getProfileCIDInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/data-profile', [
            'cid' => 'a'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'cid' => array('The cid must be a number.', 'The cid must not be greater than 7 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Profile-CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getProfileCIDInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/cid/data-profile', [
            'cid' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'cid' => array('The cid field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Profile-CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getUnmappingProfileCID(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/cid/unmapping-profile?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get List Unmapping Profile-CID Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Unmapping Profile-CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Unmapping Profile-CID Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function updateProfileCIDSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'id' => '0',
            'name' => 'Profile-0',
            'desc' => 'Deskripsi Profile-0',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/profile/add', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
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
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update-profile/0000000', [
                    'profile' => '0',
                    'modified_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile on CID Success']);
                    $I->sendDelete('/cid/delete/0000000');
                    $I->sendDelete('/profile/delete/0');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile on CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateProfileCIDNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/cid/update-profile/0', ['modified_by' => 'Tegar', 'profile' => '0']);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data CID Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Profile on CID Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateProfileCID_ProfileNotFound(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 0) {
            $loop = 1;
        } else if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update-profile/0000000', ['modified_by' => 'Tegar', 'profile' => '0']);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Profile Fee Not Found']);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile on CID Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function updateProfileCIDInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'profile' => '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'modified_by' => 'Tegar0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
            00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'
        ];
        $response_body = [
            'profile' => array('The profile must not be greater than 50 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $data_profile = [
            'id' => '0',
            'name' => 'Profile-0',
            'desc' => 'Deskripsi Profile-0',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/profile/add', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update-profile/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile on CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateProfileCIDInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'profile' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'profile' => array('The profile field is required.'),
            'modified_by' => array('The modified by field is required.')
        ];
        $data_profile = [
            'id' => '0',
            'name' => 'Profile-0',
            'desc' => 'Deskripsi Profile-0',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/profile/add', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->sendPost('/cid/add', $data);
                $response = $I->sendPutAsJson('/cid/update-profile/0000000', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/profile/delete/0');
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile on CID Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateManyCIDSuccess(ApiTester $I)
    {
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data2 = [
            'id' => '9999999',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '99999999999',
            'pic_name' => 'dummy',
            'pic_phone' => '9999999999',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '99999',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $data_profile = [
            'id' => '0',
            'name' => 'Profile-0',
            'desc' => 'Deskripsi Profile-0',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '9999999']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/profile/add', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
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
                $I->sendPost('/cid/add', $data);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/9999999');
                    $loop3 = 1;
                } else if ($countCID == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->sendPost('/cid/add', $data2);
                $response = $I->sendPutAsJson('/cid/many-update-profile', ['cid' => ['0000000', '9999999'], 'profile' => '0', 'modified_by' => 'Tegar']);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Success']);
                    $I->sendDelete('/profile/delete/0');
                    $I->sendDelete('/cid/delete/0000000');
                    $I->sendDelete('/cid/delete/9999999');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateManyCIDButSomeCIDNotRegistered(ApiTester $I)
    {
        $data_profile = [
            'id' => '0',
            'name' => 'Profile-0',
            'desc' => 'Deskripsi Profile-0',
            'created_by' => 'Tegar'
        ];
        $data = [
            'id' => '0000000',
            'profile' => '0',
            'name' => 'dummy',
            'address' => 'dummy',
            'phone' => '0000000000',
            'pic_name' => 'dummy',
            'pic_phone' => '0000000',
            'type' => 0,
            'fund_type' => 0,
            'terminal_type' => '0000',
            'minimal_deposit' => 0,
            'short_id' => 'DMY',
            'counter_code'=> '0',
            'alias_id'=> '00000',
            'alias_name' => '00000',
            'created_by' => 'Tegar'
        ];
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        $I->amConnectedToDatabase('db_recon');
        $count = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '0000000']);
        $countCID = $I->grabNumRecords('CSCCORE_DOWN_CENTRAL', ['CSC_DC_ID' => '9999999']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }
        switch ($loop) {
            case 1:
                $I->sendPost('/profile/add', $data_profile);
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->sendPost('/cid/add', $data);
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/9999999');
                    $loop3 = 1;
                } else if ($countCID == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPutAsJson('/cid/many-update-profile', ['cid' => ['0000000', '9999999'], 'profile' => '0', 'modified_by' => 'Tegar']);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Success but Some CID Not Registered',
                        'result_data' => ['cid_not_registered' => array()]]);
                    $I->sendDelete('/profile/delete/0');
                    $I->sendDelete('/cid/delete/0000000');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function updateManyCIDProfileNotFound(ApiTester $I)
    {
        $countProfile = $I->grabNumRecords('CSCCORE_PROFILE_FEE', ['CSC_PROFILE_ID' => '0']);
        if ($countProfile == 1) {
            $I->sendDelete('/profile/delete/0');
            $loop = 1;
        } else if ($countProfile == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }
        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop2 = 1;
                } else if ($count == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countCID == 1) {
                    $I->sendDelete('/cid/delete/0000000');
                    $loop3 = 1;
                } else if ($countCID == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPutAsJson('/cid/many-update-profile', ['cid' => ['0000000', '9999999'], 'profile' => '0', 'modified_by' => 'Tegar']);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Success but Some CID Not Registered']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop) {
            case 1:
                $response = $I->sendPutAsJson('/cid/many-update-profile', ['cid' => ['0000000', '9999999'], 'profile' => '0', 'modified_by' => 'Tegar']);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Profile Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function updateManyCIDProfileNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/cid/many-update-profile', [
            'cid' => ['0000000000', '9999999999'],
            'profile' => '0000000000000000000000000000000000000000000000000000000000000000',
            'modified_by' => 'Tegar1234567890123456789012345678901234567890123456789012345678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'modified_by' => array('The modified by must not be greater than 50 characters.'),
                'profile' => array('The profile must be between 1 and 50 digits.'),
                'cid.0' => array('The cid.0 must be between 1 and 7 digits.'),
                'cid.1' => array('The cid.1 must be between 1 and 7 digits.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateManyCIDInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/cid/many-update-profile', [
            'cid' => '',
            'profile' => '',
            'modified_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'cid' => array('The cid field is required.'),
                'profile' => array('The profile field is required.'),
                'modified_by' => array('The modified by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Profile with many CID Failed']);
        } else {
            $I->seeResponseCodeIs(400);
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
