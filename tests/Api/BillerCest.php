<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class BillerCest
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
        $response = $I->sendGetAsJson('/biller/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Biller Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/biller/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Biller Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/biller/filter?name=&gop=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Biller Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/biller/trash?name=&gop=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Trash Biller Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Trash Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listGOP(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/biller/list-gop');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get Data Group Of Product Success'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Of Product Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Group Of Product Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $countData = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($countData == 0) {
                    $loop2 = 1;
                } else if ($countData == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Success']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
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
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_2 = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY-2',
            'name' => 'DUMMY-2',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $data_gop_2 = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY-2',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY-2',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY-2'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $countGOP_2 = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY-2']);
        $countData = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                if ($countGOP_2 == 1) {
                    $loop2 = 1;
                } else if ($countGOP_2 == 0) {
                    $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop_2);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countData == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countData == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->sendPost('/biller/add', $data_2);
                $response = $I->sendPostAsJson('/biller/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Biller Exists']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataGOPExists(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_2 = [
            'id' => 'DMY-2',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $countData = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countData_2 = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DMY-2']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                if ($countData == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                if ($countData_2 == 1) {
                    $I->sendDelete('/biller/delete/DMY-2');
                    $loop3 = 1;
                } else if ($countData_2 == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPostAsJson('/biller/add', $data_2);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Group Of Product Exists']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
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
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_2 = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY-2',
            'name' => 'DUMMY-2',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $data_gop_2 = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY-2',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY-2',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY-2'
        ];
        $loop3 = null;
        $loop4 = null;
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $countGOP_2 = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY-2']);
        $countData = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY', 'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY-2', 'CSC_BILLER_DELETED_DT !=' => null]);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                if ($countGOP_2 == 1) {
                    $loop2 = 1;
                } else if ($countGOP_2 == 0) {
                    $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop_2);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countData == 1) {
                    $loop4 = 1;
                } else if ($countData == 0) {
                    $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/biller/delete/DUMMY', ['deleted_by' => 'Tegar']);
                    $loop5 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/biller/add', $data);
                    $I->sendPut('/biller/delete/DUMMY', ['deleted_by' => 'Tegar']);
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add', $data_2);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataGOPNotFound(ApiTester $I)
    {
        $data = [
            'id' => 'DMY2',
            'gop' => 'DUMMY-101234143',
            'name' => 'DUMMY-2',
            'created_by' => 'Tegar'
        ];
        $response = $I->sendPostAsJson('/biller/add', $data);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Group Of Product Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add', [
            'id' => 'DUMMY-123',
            'gop' => 'DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'name' => 'DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'created_by' => 'Tegar1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id must not be greater than 5 characters.'),
                'gop' => array('The gop must not be greater than 50 characters.'),
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add', [
            'id' => '',
            'gop' => '',
            'name' => '',
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id field is required.'),
                'gop' => array('The gop field is required.'),
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
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DMY99',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY-99',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-11-01 16:20:46'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY-99',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY-99',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY-99'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY-99']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DMY99']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DMY99');
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
                $I->haveInDatabase('CSCCORE_BILLER', $data);
                $response = $I->sendPostAsJson('/biller/get-data', [
                    'id' => 'DMY99'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/get-data', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/get-data', [
            'id' => 'DUMMY-123'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/get-data', [
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_update = [
            'name' => 'DUMMY-UPDATED',
            'modified_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPutAsJson('/biller/update/DUMMY', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Biller Success']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDataNotFound(ApiTester $I)
    {
        $data_update = [
            'name' => 'DUMMY-UPDATED',
            'modified_by' => 'Tegar'
        ];
        $response = $I->sendPutAsJson('/biller/update/0', $data_update);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'name' => 'DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
            1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'modified_by' => 'Tegar1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890'
        ];
        $response_body = [
            'name' => array('The name must not be greater than 100 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPutAsJson('/biller/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
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
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPutAsJson('/biller/update/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPutAsJson('/biller/delete/DUMMY', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Biller Success']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/biller/delete/0', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            1234567890312456789013245678901234567890123467890213456789031246789021346789021346789012345678901'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPutAsJson('/biller/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
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
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPutAsJson('/biller/delete/DUMMY', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/biller/add', $data);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendDeleteAsJson('/biller/delete/DUMMY');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/biller/delete/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listAccountBillerSuccess(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $data_bank = [
            'CSC_BANK_CODE' => '000',
            'CSC_BANK_NAME' => 'BANK DUMMY',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => '0000000000',
            'CSC_ACCOUNT_BANK' => '000',
            'CSC_ACCOUNT_NAME' => 'ACCOUNT DUMMY',
            'CSC_ACCOUNT_OWNER' => 'OWNER DUMMY',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account_biller = [
            'CSC_BA_ID' => '00000',
            'CSC_BA_ACCOUNT' => '0000000000',
            'CSC_BA_BILLER' => 'DUMMY'
        ];
        $countBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => '000']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => '0000000000']);
        $countAccountBiller = $I->grabNumRecords('CSCCORE_BILLER_ACCOUNT', ['CSC_BA_ID' => '00000']);
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countBank == 1) {
            $I->sendDelete('/bank/delete/000');
            $I->haveInDatabase('CSCCORE_BANK', $data_bank);
            $loop = 1;
        } else if ($countBank == 0) {
            $I->haveInDatabase('CSCCORE_BANK', $data_bank);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($countAccount == 1) {
                    $I->sendDelete('/account/delete/0000000000');
                    $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                    $loop2 = 1;
                } else if ($countAccount == 0) {
                    $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countAccountBiller == 1) {
                    $loop3 = 1;
                } else if ($countAccountBiller == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_ACCOUNT', $data_account_biller);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                if ($countGOP == 1) {
                    $loop4 = 1;
                } else if ($countGOP == 0) {
                    $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop5 = 1;
                } else if ($count == 0) {
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPostAsJson('/biller/list-account', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Account-Biller Success',
                        'result_data' => ['per_page' => 50]]);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listAccountBiller_BillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-account', [
            'biller_id' => '0',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listAccountBillerNotFound(ApiTester $I)
    {
        $data = [
            'id' => 'DUMMY',
            'gop' => 'DUMMY',
            'name' => 'DUMMY',
            'created_by' => 'Tegar'
        ];
        $data_gop = [
            'CSC_GOP_PRODUCT_NAME' => 'DUMMY',
            'CSC_GOP_PRODUCT_GROUP' => 'DUMMY',
            'CSC_GOP_PRODUCT_PARENT_PRODUCT' => 'DUMMY'
        ];
        $countGOP = $I->grabNumRecords('CSCCORE_GROUP_OF_PRODUCT', ['CSC_GOP_PRODUCT_NAME' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countGOP == 1) {
            $loop = 1;
        } else if ($countGOP == 0) {
            $I->haveInDatabase('CSCCORE_GROUP_OF_PRODUCT', $data_gop);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                if ($count == 0) {
                    $loop2 = 1;
                } else if ($count == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->sendPost('/biller/add', $data);
                $response = $I->sendPostAsJson('/biller/list-account', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Account-Biller Not Found']);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listAccountBillerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-account', [
            'biller_id' => 'DUMMY12345',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAccountBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-account', [
            'biller_id' => '',
            'items' => null
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormAccountBillerSuccess(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/biller/list-add-account', [
            'items' => '50'
        ]);
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get List Add Account-Biller Success']);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Get List Add Account-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function getAccountBillerSuccess(ApiTester $I)
    {
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => '0000000000',
            'CSC_ACCOUNT_BANK' => '000',
            'CSC_ACCOUNT_NAME' => 'ACCOUNT DUMMY',
            'CSC_ACCOUNT_OWNER' => 'OWNER DUMMY',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_bank = [
            'CSC_BANK_CODE' => '000',
            'CSC_BANK_NAME' => 'BANK DUMMY',
            'CSC_BANK_CREATED_BY' => 'Tegar',
            'CSC_BANK_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBank = $I->grabNumRecords('CSCCORE_BANK', ['CSC_BANK_CODE' => '000']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => '0000000000']);
        if ($countBank == 1) {
            $I->sendDelete('/bank/delete/000');
            $I->haveInDatabase('CSCCORE_BANK', $data_bank);
            $loop = 1;
        } else if ($countBank == 0) {
            $I->haveInDatabase('CSCCORE_BANK', $data_bank);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($countAccount == 1) {
                    $I->sendDelete('/account/delete/0000000000');
                    $loop2 = 1;
                } else if ($countAccount == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
                $response = $I->sendPostAsJson('/biller/data-account', [
                    'account' => '0000000000'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Account-Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getAccountBillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/data-account', [
            'account' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getAccountBillerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/data-account', [
            'account' => '1111111111222222222233333'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'account' => array('The account must not be greater than 20 characters.') 
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getAccountBillerInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/data-account', [
            'account' => 'abcedfg'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'account' => array('The account must be a number.', 'The account must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getAccountBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/data-account', [
            'account' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'account' => array('The account field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addAccountBillerSuccess(ApiTester $I)
    {
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => '0000000000',
            'CSC_ACCOUNT_BANK' => '000',
            'CSC_ACCOUNT_NAME' => 'ACCOUNT DUMMY',
            'CSC_ACCOUNT_OWNER' => 'OWNER DUMMY',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => '0000000000']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_ACCOUNT', ['CSC_BA_ACCOUNT' => '0000000000', 'CSC_BA_BILLER' => 'DUMMY']);
        $dataBaID = $I->grabFromDatabase('CSCCORE_BILLER_ACCOUNT', 'CSC_BA_ID', ['CSC_BA_ACCOUNT' => '0000000000', 'CSC_BA_BILLER' => 'DUMMY']);
        if ($countAccount == 1) {
            $I->sendDelete('/account/delete/0000000000');
            $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
            $loop = 1;
        } else if ($countAccount == 0) {
            $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                if ($countBiller == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $I->haveInDatabase('CSCCORE_BILLER', $data);
                    $loop2 = 1;
                } else if ($countBiller == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER', $data);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($count == 1) {
                    $I->sendDelete('/biller/delete-account/'.$dataBaID);
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
                $response = $I->sendPostAsJson('/biller/add-account', [
                    'biller_id' => 'DUMMY',
                    'account' => '0000000000'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Success']);
                    $dataBaInsert = $I->grabFromDatabase('CSCCORE_BILLER_ACCOUNT', 'CSC_BA_ID', ['CSC_BA_ACCOUNT' => '0000000000', 'CSC_BA_BILLER' => 'DUMMY']);
                    $I->sendDelete('/biller/delete-account/'.$dataBaInsert);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addAccountBiller_BillerNotFound(ApiTester $I)
    {
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => '0000000000',
            'CSC_ACCOUNT_BANK' => '000',
            'CSC_ACCOUNT_NAME' => 'ACCOUNT DUMMY',
            'CSC_ACCOUNT_OWNER' => 'OWNER DUMMY',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => '0000000000']);
        if ($countAccount == 1) {
            $loop = 1;
        } else if ($countAccount == 0) {
            $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add-account', [
                    'biller_id' => '0',
                    'account' => '0000000000'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addAccountBiller_AccountNotFound(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countBiller == 1) {
            $loop = 1;
        } else if ($countBiller == 0) {
            $I->haveInDatabase('CSCCORE_BILLER', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add-account', [
                    'biller_id' => 'DUMMY',
                    'account' => '0'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Account Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addAccountBiller_AccountExists(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_2 = [
            'CSC_BILLER_ID' => 'DMY32',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => '0000000000',
            'CSC_ACCOUNT_BANK' => '000',
            'CSC_ACCOUNT_NAME' => 'ACCOUNT DUMMY',
            'CSC_ACCOUNT_OWNER' => 'OWNER DUMMY',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account_biller = [
            'CSC_BA_ID' => '00000',
            'CSC_BA_ACCOUNT' => '0000000000',
            'CSC_BA_BILLER' => 'DUMMY'
        ];
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => '0000000000']);
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countBiller_2 = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DMY32']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_ACCOUNT', ['CSC_BA_ACCOUNT' => '0000000000', 'CSC_BA_BILLER' => 'DUMMY']);
        if ($countAccount == 1) {
            $loop = 1;
        } else if ($countAccount == 0) {
            $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                if ($countBiller == 1) {
                    $loop2 = 1;
                } else if ($countBiller == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER', $data);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countBiller_2 == 1) {
                    $loop3 = 1;
                } else if ($countBiller_2 == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER', $data_2);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                if ($count == 1) {
                    $loop4 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_ACCOUNT', $data_account_biller);
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add-account', [
                    'biller_id' => 'DMY32',
                    'account' => '0000000000'
                ]);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Account Exists']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addAccountBillerExists(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account = [
            'CSC_ACCOUNT_NUMBER' => '0000000000',
            'CSC_ACCOUNT_BANK' => '000',
            'CSC_ACCOUNT_NAME' => 'ACCOUNT DUMMY',
            'CSC_ACCOUNT_OWNER' => 'OWNER DUMMY',
            'CSC_ACCOUNT_TYPE' => 0,
            'CSC_ACCOUNT_CREATED_BY' => 'Tegar',
            'CSC_ACCOUNT_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_account_biller = [
            'CSC_BA_ID' => '00000',
            'CSC_BA_ACCOUNT' => '0000000000',
            'CSC_BA_BILLER' => 'DUMMY'
        ];
        $countAccount = $I->grabNumRecords('CSCCORE_ACCOUNT', ['CSC_ACCOUNT_NUMBER' => '0000000000']);
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_ACCOUNT', ['CSC_BA_ACCOUNT' => '0000000000', 'CSC_BA_BILLER' => 'DUMMY']);
        if ($countAccount == 1) {
            $loop = 1;
        } else if ($countAccount == 0) {
            $I->haveInDatabase('CSCCORE_ACCOUNT', $data_account);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                if ($countBiller == 1) {
                    $loop2 = 1;
                } else if ($countBiller == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER', $data);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($count == 1) {
                    $loop3 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_ACCOUNT', $data_account_biller);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add-account', [
                    'biller_id' => 'DUMMY',
                    'account' => '0000000000'
                ]);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Account-Biller Exists']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addAccountBillerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add-account', [
            'biller_id' => 'DUMMY12345',
            'account' => '00000000000000000000000000000000'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id must not be greater than 5 characters.'),
                'account' => array('The account must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addAccountBillerInvalidNumeric(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add-account', [
            'biller_id' => 'DUMMY',
            'account' => 'abcedfg'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'account' => array('The account must be a number.', 'The account must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addAccountBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add-account', [
            'biller_id' => '',
            'account' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id field is required.'),
                'account' => array('The account field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteAccountBillerSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BA_ID' => '123456',
            'CSC_BA_ACCOUNT' => '0000000000',
            'CSC_BA_BILLER' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_BILLER_ACCOUNT', ['CSC_BA_ID' => '123456']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_BILLER_ACCOUNT', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }
        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/biller/delete-account/123456');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Account-Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Account-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteAccountBillerNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/biller/delete-account/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Account-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Data Account-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listProductBillerSuccess(ApiTester $I)
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
                    $loop3 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $response = $I->sendPostAsJson('/biller/list-product', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Biller Success', 
                        'result_data' => ['per_page' => 50]]);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listProductBiller_BillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-product', [
            'biller_id' => '0',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listProductBillerNotFound(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_BILLER', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/biller/list-product', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Product-Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listProductBillerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-product', [
            'biller_id' => 'DUMMY12345',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listProductBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-product', [
            'biller_id' => '',
            'items' => null
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listAddFormProductBillerSuccess(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-add-product', [
            'items' => '50'
        ]);
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get List Add Product-Biller Success']);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Add Product-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Add Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addProductBillerSuccess(ApiTester $I)
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
        $data_product_2 = [
            'CSC_TD_NAME' => 'DUMMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY-2',
            'CSC_TD_ALIASNAME' => 'DUMMY-2',
            'CSC_TD_DESC' => 'DUMMY-2',
            'CSC_TD_TABLE' => 'DUMMY-2',
            'CSC_TD_CRITERIA' => 'DUMMY-2',
            'CSC_TD_FINDCRITERIA' => 'DUMMY-2',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY-2',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY-2',
            'CSC_TD_BANK_COLUMN' => 'DUMMY-2',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY-2',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY-2',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY-2',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY-2',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY-2',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY-2',
            'CSC_TD_DATE_COLUMN' => 'DUMMY-2',
            'CSC_TD_NREK_COLUMN' => 'DUMMY-2',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY-2',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY-2',
            'CSC_TD_TABLE_ARCH' => 'DUMMY-2',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY-2',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY-2',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY-2',
            'CSC_TD_TYPE_TRX' => 'DUMMY-2',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY-2']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
        $dataBpID = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        $dataBpID_2 = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
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
                    $I->sendDelete('/product/delete/DUMMY-2');
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
                if ($countBiller == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop3 = 1;
                } else if ($countBiller == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                if ($count == 1) {
                    $I->sendDelete('/biller/delete-product/'.$dataBpID);
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
                    $I->sendDelete('/biller/delete-product/'.$dataBpID_2);
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
                $response = $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => 'DUMMY',
                    'product' => ['DUMMY', 'DUMMY-2']
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Success']);
                    $dataBpInsert = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
                    $dataBpInsert_2 = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
                    $I->sendDelete('/biller/delete-product/'.$dataBpInsert);
                    $I->sendDelete('/biller/delete-product/'.$dataBpInsert_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addProductBiller_BillerNotFound(ApiTester $I)
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
            $loop = 1;
        } else if ($countProduct == 0) {
            $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => '0',
                    'product' => ['DUMMY']
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addProductBillerExists(ApiTester $I)
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
        $data_product_2 = [
            'CSC_TD_NAME' => 'DUMMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY-2',
            'CSC_TD_ALIASNAME' => 'DUMMY-2',
            'CSC_TD_DESC' => 'DUMMY-2',
            'CSC_TD_TABLE' => 'DUMMY-2',
            'CSC_TD_CRITERIA' => 'DUMMY-2',
            'CSC_TD_FINDCRITERIA' => 'DUMMY-2',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY-2',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY-2',
            'CSC_TD_BANK_COLUMN' => 'DUMMY-2',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY-2',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY-2',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY-2',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY-2',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY-2',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY-2',
            'CSC_TD_DATE_COLUMN' => 'DUMMY-2',
            'CSC_TD_NREK_COLUMN' => 'DUMMY-2',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY-2',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY-2',
            'CSC_TD_TABLE_ARCH' => 'DUMMY-2',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY-2',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY-2',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY-2',
            'CSC_TD_TYPE_TRX' => 'DUMMY-2',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_biller = [
            'CSC_BP_ID' => '00000',
            'CSC_BP_PRODUCT' => 'DUMMY',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $data_product_biller_2 = [
            'CSC_BP_ID' => '11111',
            'CSC_BP_PRODUCT' => 'DUMMY-2',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY-2']);
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
        if ($countProduct == 1) {
            $loop = 1;
        } else if ($countProduct == 0) {
            $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                if ($countProduct_2 == 1) {
                    $loop2 = 1;
                } else if ($countProduct_2 == 0) {
                    $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countBiller == 1) {
                    $loop3 = 1;
                } else if ($countBiller == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                if ($count == 1) {
                    $loop4 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data_product_biller);
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                if ($count_2 == 1) {
                    $loop5 = 1;
                } else if ($count_2 == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data_product_biller_2);
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => 'DUMMY',
                    'product' => ['DUMMY', 'DUMMY-2']
                ]);
                $response = $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => 'DUMMY',
                    'product' => ['DUMMY-2']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Success But Some Product Exists',
                        'result_data' => ['product_exists' => array()]]);
                    $dataBpID = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
                    $dataBpID_2 = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
                    $I->sendDelete('/biller/delete-product/'.$dataBpID);
                    $I->sendDelete('/biller/delete-product/'.$dataBpID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addProductBillerNotFound(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countBiller == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        }
        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                $response = $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => 'DUMMY',
                    'product' => ['DUMMY-100', 'Product-1']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Success But Some Product Not Registered',
                        'result_data' => ['product_not_registered' => array()]]);
                    $I->sendDelete('/biller/delete/DUMMY');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addProductBillerCannotProcessed(ApiTester $I) 
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
        $data_product_2 = [
            'CSC_TD_NAME' => 'DUMMY-2',
            'CSC_TD_GROUPNAME' => 'DUMMY-2',
            'CSC_TD_ALIASNAME' => 'DUMMY-2',
            'CSC_TD_DESC' => 'DUMMY-2',
            'CSC_TD_TABLE' => 'DUMMY-2',
            'CSC_TD_CRITERIA' => 'DUMMY-2',
            'CSC_TD_FINDCRITERIA' => 'DUMMY-2',
            'CSC_TD_BANK_CRITERIA' => 'DUMMY-2',
            'CSC_TD_CENTRAL_CRITERIA' => 'DUMMY-2',
            'CSC_TD_BANK_COLUMN' => 'DUMMY-2',
            'CSC_TD_CENTRAL_COLUMN' => 'DUMMY-2',
            'CSC_TD_TERMINAL_COLUMN' => 'DUMMY-2',
            'CSC_TD_SUBID_COLUMN' => 'DUMMY-2',
            'CSC_TD_SUBNAME_COLUMN' => 'DUMMY-2',
            'CSC_TD_SWITCH_REFNUM_COLUMN' => 'DUMMY-2',
            'CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN' => 'DUMMY-2',
            'CSC_TD_DATE_COLUMN' => 'DUMMY-2',
            'CSC_TD_NREK_COLUMN' => 'DUMMY-2',
            'CSC_TD_NBILL_COLUMN' => 'DUMMY-2',
            'CSC_TD_BILL_AMOUNT_COLUMN' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1' => 'DUMMY-2',
            'CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2' => 'DUMMY-2',
            'CSC_TD_TABLE_ARCH' => 'DUMMY-2',
            'CSC_TD_BANK_GROUPBY' => 'DUMMY-2',
            'CSC_TD_CENTRAL_GROUPBY' => 'DUMMY-2',
            'CSC_TD_TERMINAL_GROUPBY' => 'DUMMY-2',
            'CSC_TD_TYPE_TRX' => 'DUMMY-2',
            'CSC_TD_CREATED_BY' => 'Tegar',
            'CSC_TD_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_product_biller = [
            'CSC_BP_ID' => '00000',
            'CSC_BP_PRODUCT' => 'DUMMY',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $data_product_biller_2 = [
            'CSC_BP_ID' => '11111',
            'CSC_BP_PRODUCT' => 'DUMMY-2',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $countProduct = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY']);
        $countProduct_2 = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'DUMMY-2']);
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
        $count_2 = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
        if ($countProduct == 1) {
            $loop = 1;
        } else if ($countProduct == 0) {
            $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(202);
        }

        switch ($loop) {
            case 1:
                if ($countProduct_2 == 1) {
                    $loop2 = 1;
                } else if ($countProduct_2 == 0) {
                    $I->haveInDatabase('CSCCORE_TRANSACTION_DEFINITION', $data_product_2);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                if ($countBiller == 1) {
                    $loop3 = 1;
                } else if ($countBiller == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER', $data_biller);
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                if ($count == 1) {
                    $loop4 = 1;
                } else if ($count == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data_product_biller);
                    $loop4 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop4) {
            case 1:
                if ($count_2 == 1) {
                    $loop5 = 1;
                } else if ($count_2 == 0) {
                    $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data_product_biller_2);
                    $loop5 = 1;
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
        switch ($loop5) {
            case 1:
                $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => 'DUMMY',
                    'product' => ['DUMMY', 'DUMMY-2']
                ]);
                $response = $I->sendPostAsJson('/biller/add-product', [
                    'biller_id' => 'DUMMY',
                    'product' => ['Product-1', 'DUMMY-2']
                ]);
                if ($response['result_code'] == 202) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(202);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Success But Some Product Cannot Processed',
                        'result_data' => ['product_exists' => array(), 'product_not_registered' => array()]]);
                    $dataBpID = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY', 'CSC_BP_BILLER' => 'DUMMY']);
                    $dataBpID_2 = $I->grabFromDatabase('CSCCORE_BILLER_PRODUCT', 'CSC_BP_ID', ['CSC_BP_PRODUCT' => 'DUMMY-2', 'CSC_BP_BILLER' => 'DUMMY']);
                    $I->sendDelete('/biller/delete-product/'.$dataBpID);
                    $I->sendDelete('/biller/delete-product/'.$dataBpID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(202);
                }
                break;
        }
    }

    public function addProductBillerInvalidLength(ApiTester $I) 
    {
        $response = $I->sendPostAsJson('/biller/add-product', [
            'biller_id' => 'DUMMY12345',
            'product' => ['DUMMY1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
                        1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890']
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id must not be greater than 5 characters.'),
                'product.0' => array('The product.0 must not be greater than 100 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addProductBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add-product', [
            'biller_id' => '',
            'product' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id field is required.'),
                'product' => array('The product field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteProductBillerSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BP_ID' => '123456',
            'CSC_BP_PRODUCT' => 'DUMMY',
            'CSC_BP_BILLER' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_BILLER_PRODUCT', ['CSC_BP_ID' => '123456']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->haveInDatabase('CSCCORE_BILLER_PRODUCT', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }
        
        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/biller/delete-product/123456');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product-Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteProductBillerNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/biller/delete-product/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Data Product-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listCalendarBillerSuccess(ApiTester $I)
    {
        $data_biller = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_calendar_biller = [
            'CSC_BC_ID' => '00000',
            'CSC_BC_CALENDAR' => 'DUMMY',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $countCalendarBiller = $I->grabNumRecords('CSCCORE_BILLER_CALENDAR', ['CSC_BC_ID' => '00000']);
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
                if ($countCalendar == 1) {
                    $I->sendDelete('/calendar/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countCalendar == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                if ($countCalendarBiller == 1) {
                    $I->sendDelete('/biller/delete-calendar/00000');
                    $loop3 = 1;
                } else if ($countCalendarBiller == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_CALENDAR', $data_calendar_biller);
                $response = $I->sendPostAsJson('/biller/list-calendar', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Calendar-Biller Success', 
                        'result_data' => ['per_page' => 50]]);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function listCalendarBiller_BillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-calendar', [
            'biller_id' => '0',
            'items' => 50
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function listCalendarBillerNotFound(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $count = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($count == 0) {
            $loop = 1;
        } else if ($count == 1) {
            $I->sendDelete('/biller/delete/DUMMY');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data);
                $response = $I->sendPostAsJson('/biller/list-calendar', [
                    'biller_id' => 'DUMMY',
                    'items' => 50
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Calendar-Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function listCalendarBillerInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-calendar', [
            'biller_id' => 'DUMMY12345',
            'items' => 50
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id must not be greater than 5 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function listCalendarBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/list-calendar', [
            'biller_id' => '',
            'items' => null
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getCalendarBillerSuccess(ApiTester $I)
    {
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
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
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                $response = $I->sendPostAsJson('/biller/data-calendar', [
                    'id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar-Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getCalendarBillerNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/data-calendar', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getCalendarBillerInvalidLength(ApiTester $I) 
    {
        $response = $I->sendPostAsJson('/biller/data-calendar', [
            'id' => '1111111111222222222233333'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getCalendarBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/data-calendar', [
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addCalendarBillerSuccess(ApiTester $I)
    {
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_CALENDAR', ['CSC_BC_CALENDAR' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
        $dataBcID = $I->grabFromDatabase('CSCCORE_BILLER_CALENDAR', 'CSC_BC_ID', ['CSC_BC_CALENDAR' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
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
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                if ($countBiller == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countBiller == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data);
                if ($count == 1) {
                    $I->sendDelete('/biller/delete-calendar/'.$dataBcID);
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
                $response = $I->sendPostAsJson('/biller/add-calendar', [
                    'biller_id' => 'DUMMY',
                    'calendar' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Success']);
                    $dataBcID_2 = $I->grabFromDatabase('CSCCORE_BILLER_CALENDAR', 'CSC_BC_ID', ['CSC_BC_CALENDAR' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
                    $I->sendDelete('/biller/delete-calendar/'.$dataBcID_2);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addCalendarBiller_BillerNotFound(ApiTester $I)
    {
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
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
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                $response = $I->sendPostAsJson('/biller/add-calendar', [
                    'biller_id' => '0',
                    'calendar' => 'DUMMY'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Biller Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addCalendarBiller_CalendarNotFound(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        if ($countBiller == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($countBiller == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(404);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data);
                $response = $I->sendPostAsJson('/biller/add-calendar', [
                    'biller_id' => 'DUMMY',
                    'calendar' => '0'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function addCalendarBillerExists(ApiTester $I)
    {
        $data = [
            'CSC_BILLER_ID' => 'DUMMY',
            'CSC_BILLER_GROUP_PRODUCT' => 'DUMMY',
            'CSC_BILLER_NAME' => 'DUMMY',
            'CSC_BILLER_CREATED_BY' => 'Tegar',
            'CSC_BILLER_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_calendar_biller = [
            'CSC_BC_ID' => '00000',
            'CSC_BC_CALENDAR' => 'DUMMY',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $countBiller = $I->grabNumRecords('CSCCORE_BILLER', ['CSC_BILLER_ID' => 'DUMMY']);
        $count = $I->grabNumRecords('CSCCORE_BILLER_CALENDAR', ['CSC_BC_CALENDAR' => 'DUMMY', 'CSC_BC_BILLER' => 'DUMMY']);
        if ($countCalendar == 1) {
            $I->sendDelete('/calendar/delete/DUMMY');
            $loop = 1;
        } else if ($countCalendar == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                if ($countBiller == 1) {
                    $I->sendDelete('/biller/delete/DUMMY');
                    $loop2 = 1;
                } else if ($countBiller == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER', $data);
                if ($count == 1) {
                    $I->sendDelete('/biller/delete-calendar/00000');
                    $loop3 = 1;
                } else if ($count == 0) {
                    $loop3 = 1;
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
        switch ($loop3) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_CALENDAR', $data_calendar_biller);
                $response = $I->sendPostAsJson('/biller/add-calendar', [
                    'biller_id' => 'DUMMY',
                    'calendar' => 'DUMMY'
                ]);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Calendar-Biller Exists']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addCalendarBillerInvalidLength(ApiTester $I) 
    {
        $response = $I->sendPostAsJson('/biller/add-calendar', [
            'biller_id' => 'DUMMY12345',
            'calendar' => '00000000000000000000000000000000'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id must not be greater than 5 characters.'),
                'calendar' => array('The calendar must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addCalendarBillerInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/biller/add-calendar', [
            'biller_id' => '',
            'calendar' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'biller_id' => array('The biller id field is required.'),
                'calendar' => array('The calendar field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Calendar-Biller Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function viewDetailCalendarSuccess(ApiTester $I)
    {
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $data_days = [
            'CSC_CD_ID' => 'DUMMY',
            'CSC_CD_CALENDAR' => 'DUMMY',
            'CSC_CD_DATE' => '2022-12-01',
            'CSC_CD_DESC' => 'DUMMY DAYS'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
        $countDays = $I->grabNumRecords('CSCCORE_CALENDAR_DAYS', ['CSC_CD_ID' => 'DUMMY']);
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
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                if ($countDays == 1) {
                    $I->sendDelete('/calendar/delete-day/DUMMY');
                    $loop2 = 1;
                } else if ($countDays == 0) {
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $I->haveInDatabase('CSCCORE_CALENDAR_DAYS', $data_days);
                $response = $I->sendPostAsJson('/calendar/view', [
                    'id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Detail Calendar Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Detail Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function viewDetailCalendar_CalendarNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/view', [
            'id' => '0'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Detail Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function viewDetailCalendarNotFound(ApiTester $I)
    {
        $data_calendar = [
            'CSC_CAL_ID' => 'DUMMY',
            'CSC_CAL_NAME' => 'CALENDAR DUMMY',
            'CSC_CAL_DEFAULT' => 1,
            'CSC_CAL_CREATED_BY' => 'Tegar',
            'CSC_CAL_CREATED_DT' => '2022-12-01 07:00:00'
        ];
        $countCalendar = $I->grabNumRecords('CSCCORE_CALENDAR', ['CSC_CAL_ID' => 'DUMMY']);
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
                $I->haveInDatabase('CSCCORE_CALENDAR', $data_calendar);
                $response = $I->sendPostAsJson('/calendar/view', [
                    'id' => 'DUMMY'
                ]);
                if ($response['result_code'] == 404) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(404);
                    $I->seeResponseContainsJson(['result_message' => 'Data Detail Calendar Not Found']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Detail Calendar Failed']);
                } else {
                    $I->seeResponseCodeIs(404);
                }
                break;
        }
    }

    public function viewDetailCalendarInvalidLength(ApiTester $I) 
    {
        $response = $I->sendPostAsJson('/calendar/view', [
            'id' => '11111111112222222222333331231231232132131231231232'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'id' => array('The id must not be greater than 20 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Detail Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function viewDetailCalendarInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/calendar/view', [
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
            $I->seeResponseContainsJson(['result_message' => 'Get Data Detail Calendar Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function deleteCalendarBillerSuccess(ApiTester $I)
    {
        $data = [
            'CSC_BC_ID' => '123456',
            'CSC_BC_CALENDAR' => '0000000000',
            'CSC_BC_BILLER' => 'DUMMY'
        ];
        $count = $I->grabNumRecords('CSCCORE_BILLER_CALENDAR', ['CSC_BC_ID' => '123456']);
        if ($count == 1) {
            $I->sendDelete('/biller/delete-calendar/123456');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->haveInDatabase('CSCCORE_BILLER_CALENDAR', $data);
                $response = $I->sendDeleteAsJson('/biller/delete-calendar/123456');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar-Biller Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar-Biller Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteCalendarBillerNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/biller/delete-calendar/0');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Calendar-Biller Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Data Calendar-Biller Failed']);
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
