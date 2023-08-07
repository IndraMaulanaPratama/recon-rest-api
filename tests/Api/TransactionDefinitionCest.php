<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class TransactionDefinitionCest
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated('1|zlsttvwZE1JX1rmkfrtPUmQmUSkyaWvNPBd21mwRQGLkJgrU4DjXlAIW1m7j');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('x-auth-key', '2d398821-e738-4e77-b766-67e705aa6ae9');
        // $response = $I->grabResponse();
        // $s_response = (string)$response;
        // print $s_response;
    }

    // tests
    public function listSimple(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/product/list/simple');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Product/Area Success',
                'config' => 'simple'
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function listDetail(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/product/list/detail?items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson([
                'result_message' => 'Get List Product/Area Success',
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
            $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get List Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function filterData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/product/filter?name=&table=&bank=&central=&type_transaction=&isActive=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Product/Area Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Filter Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function trashData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/product/trash?name=&table=&bank=&central=&type_transaction=&isActive=&items=50');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Trash Product Success', 'result_data' => ['per_page' => 50]]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array',
                'result_data' => ['data' => 'array']
            ]);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Trash Product Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Get Filter Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function countData(ApiTester $I)
    {
        $response = $I->sendGetAsJson('/product/get-count');
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Count Total Product Success']);
        } else if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Count Data Total Product Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Count Data Total Product Failed']);
        } else {
            $I->seeResponseCodeIsServerError();
        }
    }

    public function addDataSuccess(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy',
            'central_criteria' => 'dummy',
            'bank' => 'dummy',
            'central' => 'dummy',
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy',
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-',
            'date' => 'dummy',
            'nrek' => 'dummy',
            'nbill' => 'dummy',
            'bill_amount' => 'dummy',
            'admin_amount' => 'dummy',
            'admin_amount_deduction_0' => 'dummy',
            'admin_amount_deduction_1' => 'dummy',
            'admin_amount_deduction_2' => 'dummy',
            'table_arch' => 'dummy',
            'bank_group_by' => 'dummy',
            'central_group_by' => 'dummy',
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($countData == 0) {
            $loop = 1;
        } else if ($countData == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }
        
        switch ($loop) {
            case 1:
                $response = $I->sendPostAsJson('/product/add', $data);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product/Area Success']);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function addDataExists(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(409);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPostAsJson('/product/add', $data);
                if ($response['result_code'] == 409) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(409);
                    $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Exists']);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(409);
                }
                break;
        }
    }

    public function addDataUnprocessable(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $countData = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy', 'CSC_TD_DELETED_DT !=' => null]);
        if ($countData == 1) {
            $loop2 = 1;
        } else if ($countData == 0) {
            $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(422);
        }

        switch ($loop) {
            case 1:
                if ($count == 1) {
                    $I->sendPut('/product/delete/dummy', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else if ($count == 0) {
                    $I->sendPost('/product/add', $data);
                    $I->sendPut('/product/delete/dummy', ['deleted_by' => 'Tegar']);
                    $loop2 = 1;
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
        switch ($loop2) {
            case 1:
                $response = $I->sendPostAsJson('/product/add', $data);
                if ($response['result_code'] == 422) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(422);
                    $I->seeResponseContainsJson(['result_message' => 'Unprocessable Entity']);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Insert Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(422);
                }
                break;
        }
    }

    public function addDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/add', [
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'group_name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'alias_name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'description' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'table' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'find_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'terminal' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'subid' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'subname'=> 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'switch_refnum' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'switch_payment_refnum' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'type_transaction' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'date' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'nrek' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'nbill' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bill_amount' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount_deduction_0' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount_deduction_1' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount_deduction_2' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'table_arch' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'terminal_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'created_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'name' => array('The name must not be greater than 100 characters.'),
                'group_name' => array('The group name must not be greater than 100 characters.'),
                'alias_name' => array('The alias name must not be greater than 100 characters.'),
                'description' => array('The description must not be greater than 100 characters.'),
                'table' => array('The table must not be greater than 50 characters.'),
                'criteria' => array('The criteria must not be greater than 256 characters.'),
                'find_criteria' => array('The find criteria must not be greater than 100 characters.'),
                'bank_criteria' => array('The bank criteria must not be greater than 250 characters.'),
                'central_criteria' => array('The central criteria must not be greater than 250 characters.'),
                'bank' => array('The bank must not be greater than 100 characters.'),
                'central' => array('The central must not be greater than 100 characters.'),
                'terminal' => array('The terminal must not be greater than 100 characters.'),
                'subid' => array('The subid must not be greater than 100 characters.'),
                'subname' => array('The subname must not be greater than 100 characters.'),
                'type_transaction' => array('The type transaction must not be greater than 100 characters.'),
                'date' => array('The date must not be greater than 100 characters.'),
                'nrek' => array('The nrek must not be greater than 100 characters.'),
                'nbill' => array('The nbill must not be greater than 100 characters.'),
                'bill_amount' => array('The bill amount must not be greater than 100 characters.'),
                'admin_amount' => array('The admin amount must not be greater than 100 characters.'),
                'admin_amount_deduction_0' => array('The admin amount deduction 0 must not be greater than 100 characters.'),
                'admin_amount_deduction_1' => array('The admin amount deduction 1 must not be greater than 100 characters.'),
                'admin_amount_deduction_2' => array('The admin amount deduction 2 must not be greater than 100 characters.'),
                'table_arch' => array('The table arch must not be greater than 50 characters.'),
                'bank_group_by' => array('The bank group by must not be greater than 50 characters.'),
                'central_group_by' => array('The central group by must not be greater than 50 characters.'),
                'terminal_group_by' => array('The terminal group by must not be greater than 50 characters.'),
                'created_by' => array('The created by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function addDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/add', [
            'name' => '',
            'group_name' => '',
            'alias_name' => '',
            'description' => '',
            'table' => '',
            'criteria' => '',
            'find_criteria' => '',
            'bank_criteria' => '',
            'central_criteria' => '',
            'bank' => '',
            'central' => '',
            'terminal' => '',
            'subid' => '',
            'subname'=> '',
            'switch_refnum' => '',
            'switch_payment_refnum' => '',
            'type_transaction' => '-',
            'date' => '',
            'nrek' => '',
            'nbill' => '',
            'bill_amount' => '',
            'admin_amount' => '',
            'admin_amount_deduction_0' => '',
            'admin_amount_deduction_1' => '',
            'admin_amount_deduction_2' => '',
            'table_arch' => '',
            'bank_group_by' => '',
            'central_group_by' => '',
            'terminal_group_by' => '',
            'created_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'name' => array('The name field is required.'),
                'group_name' => array('The group name field is required.'),
                'alias_name' => array('The alias name field is required.'),
                'description' => array('The description field is required.'),
                'table' => array('The table field is required.'),
                'criteria' => array('The criteria field is required.'),
                'find_criteria' => array('The find criteria field is required.'),
                'bank_criteria' => array('The bank criteria field is required.'),
                'central_criteria' => array('The central criteria field is required.'),
                'bank' => array('The bank field is required.'),
                'central' => array('The central field is required.'),
                'terminal' => array('The terminal field is required.'),
                'subid' => array('The subid field is required.'),
                'subname' => array('The subname field is required.'),
                'date' => array('The date field is required.'),
                'nrek' => array('The nrek field is required.'),
                'nbill' => array('The nbill field is required.'),
                'bill_amount' => array('The bill amount field is required.'),
                'admin_amount' => array('The admin amount field is required.'),
                'admin_amount_deduction_0' => array('The admin amount deduction 0 field is required.'),
                'admin_amount_deduction_1' => array('The admin amount deduction 1 field is required.'),
                'admin_amount_deduction_2' => array('The admin amount deduction 2 field is required.'),
                'table_arch' => array('The table arch field is required.'),
                'bank_group_by' => array('The bank group by field is required.'),
                'central_group_by' => array('The central group by field is required.'),
                'terminal_group_by' => array('The terminal group by field is required.'),
                'created_by' => array('The created by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Insert Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataSuccess(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }
        
        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPostAsJson('/product/get-data', [
                    'name' => 'dummy'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Product/Area Success']);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Get Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function getDataNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/get-data', [
            'name' => 'dummy-123-456'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/get-data', [
            'name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'name' => array('The name must not be greater than 100 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/get-data', [
            'name' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'name' => array('The name field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getColumnTableSuccess(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/data-column', [
            'table' => 'CSCMOD_VOUCHER_TRAN_MAIN'
        ]);
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Get Data List Column Name Table Success']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data List Column Name Table Failed']);
        } else {
            $I->seeResponseCodeIs(200);
        }
    }

    public function getColumnTableNotFound(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/data-column', [
            'table' => 'CSCMOD_VOUCHER_TRAN_MAIN-123-456'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data List Column Name Table Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data List Column Name Table Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function getColumnTableInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/data-column', [
            'table' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'table' => array('The table must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data List Column Name Table Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function getColumnTableInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/data-column', [
            'table' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'table' => array('The table field is required.')
                ]
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Get Data List Column Name Table Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function testDataSuccess(ApiTester $I)
    {
        $data = [
            'table' => 'CSCMOD_MF_AUTO_TRAN_MAIN',
            'criteria' => "CSM_TM_FLAG=1 and CSM_TM_SETTLE_D <> '00000000' and CSM_TM_PAID is not null and CSM_TM_PAID >='StartCCCC-MM-DD 00:00:00' and CSM_TM_PAID <='EndCCCC-MM-DD 23:59:59' and CSM_TM_BILLER='0086003'",
            'bank_criteria' => 'CSM_TM_CA in $CA$',
            'central_criteria' => 'CSM_TM_CID in $CID$',
            'bank' => 'CSM_TM_CA',
            'central' => 'CSM_TM_CID',
            'nrek' => 'count(CSM_TM_SUBID) as N_REK',
            'nbill' => 'count(CSM_TM_SUBID) as N_MONTH',
            'bill_amount' => 'sum(CSM_TM_TRANSACT_AMOUNT) as TOTAL_TAG',
            'admin_amount' => 'sum(CSM_TM_TOTAL_ADMIN_CHARGES) as TOTAL_ADM',
            'bank_group_by' => 'group by CSM_TM_CA',
            'central_group_by' => 'group by CSM_TM_CID'
        ];
        $response = $I->sendPostAsJson('/product/test-data', $data);
        if ($response['result_code'] == 200) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['result_message' => 'Test Data Product/Area Success']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Test Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(200);
        }
    }

    public function testDataInvalidLength(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/test-data', [
            'table' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'nrek' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'nbill' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bill_amount' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'table' => array('The table must not be greater than 50 characters.'),
                'criteria' => array('The criteria must not be greater than 256 characters.'),
                'bank_criteria' => array('The bank criteria must not be greater than 250 characters.'),
                'central_criteria' => array('The central criteria must not be greater than 250 characters.'),
                'bank' => array('The bank must not be greater than 100 characters.'),
                'central' => array('The central must not be greater than 100 characters.'),
                'nrek' => array('The nrek must not be greater than 100 characters.'),
                'nbill' => array('The nbill must not be greater than 100 characters.'),
                'bill_amount' => array('The bill amount must not be greater than 100 characters.'),
                'admin_amount' => array('The admin amount must not be greater than 100 characters.'),
                'bank_group_by' => array('The bank group by must not be greater than 50 characters.'),
                'central_group_by' => array('The central group by must not be greater than 50 characters.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Test Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function testDataInvalidMandatory(ApiTester $I)
    {
        $response = $I->sendPostAsJson('/product/test-data', [
            'table' => '',
            'criteria' => '',
            'bank_criteria' => '',
            'central_criteria' => '',
            'bank' => '',
            'central' => '',
            'nrek' => '',
            'nbill' => '',
            'bill_amount' => '',
            'admin_amount' => '',
            'bank_group_by' => '',
            'central_group_by' => ''
        ]);
        if ($response['result_code'] == 400) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(400);
            $I->seeResponseContainsJson(['result_data' => [
                'table' => array('The table field is required.'),
                'criteria' => array('The criteria field is required.'),
                'bank_criteria' => array('The bank criteria field is required.'),
                'central_criteria' => array('The central criteria field is required.'),
                'bank' => array('The bank field is required.'),
                'central' => array('The central field is required.'),
                'nrek' => array('The nrek field is required.'),
                'nbill' => array('The nbill field is required.'),
                'bill_amount' => array('The bill amount field is required.'),
                'admin_amount' => array('The admin amount field is required.'),
                'bank_group_by' => array('The bank group by field is required.'),
                'central_group_by' => array('The central group by field is required.')
                ]
            ]);
            $I->seeResponseMatchesJSONType([
                'result_data' => 'array'
            ]);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Test Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(400);
        }
    }

    public function updateDataSuccess(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_update = [
            'group_name' => 'dummy-update',
            'alias_name' => 'dummy-update',
            'description' => 'dummy-update',
            'table' => 'dummy-update',
            'criteria' => 'dummy-update',
            'find_criteria' => 'dummy-update',
            'bank_criteria' => 'dummy-update',
            'central_criteria' => 'dummy-update',
            'bank' => 'dummy-update',
            'central' => 'dummy-update',
            'terminal' => 'dummy-update',
            'subid' => 'dummy-update',
            'subname'=> 'dummy-update',
            'switch_refnum' => 'dummy-update',
            'switch_payment_refnum' => 'dummy-update',
            'type_transaction' => '-update',
            'date' => 'dummy-update',
            'nrek' => 'dummy-update',
            'nbill' => 'dummy-update',
            'bill_amount' => 'dummy-update',
            'admin_amount' => 'dummy-update',
            'admin_amount_deduction_0' => 'dummy-update',
            'admin_amount_deduction_1' => 'dummy-update',
            'admin_amount_deduction_2' => 'dummy-update',
            'table_arch' => 'dummy-update',
            'bank_group_by' => 'dummy-update',
            'central_group_by' => 'dummy-update',
            'terminal_group_by' => 'dummy-update',
            'modified_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPutAsJson('/product/update/dummy', $data_update);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Product/Area Success']);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function updateDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/product/update/dummy-123-456', []);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Update Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function updateDataInvalidLength(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'group_name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'alias_name' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'description' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'table' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'find_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central_criteria' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'terminal' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'subid' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'subname'=> 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'switch_refnum' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'switch_payment_refnum' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'type_transaction' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'date' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'nrek' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'nbill' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bill_amount' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount_deduction_0' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount_deduction_1' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'admin_amount_deduction_2' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'table_arch' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'bank_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'central_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'terminal_group_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890',
            'modified_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'group_name' => array('The group name must not be greater than 100 characters.'),
            'alias_name' => array('The alias name must not be greater than 100 characters.'),
            'description' => array('The description must not be greater than 100 characters.'),
            'table' => array('The table must not be greater than 50 characters.'),
            'criteria' => array('The criteria must not be greater than 256 characters.'),
            'find_criteria' => array('The find criteria must not be greater than 100 characters.'),
            'bank_criteria' => array('The bank criteria must not be greater than 250 characters.'),
            'central_criteria' => array('The central criteria must not be greater than 250 characters.'),
            'bank' => array('The bank must not be greater than 100 characters.'),
            'central' => array('The central must not be greater than 100 characters.'),
            'terminal' => array('The terminal must not be greater than 100 characters.'),
            'subid' => array('The subid must not be greater than 100 characters.'),
            'subname' => array('The subname must not be greater than 100 characters.'),
            'type_transaction' => array('The type transaction must not be greater than 100 characters.'),
            'date' => array('The date must not be greater than 100 characters.'),
            'nrek' => array('The nrek must not be greater than 100 characters.'),
            'nbill' => array('The nbill must not be greater than 100 characters.'),
            'bill_amount' => array('The bill amount must not be greater than 100 characters.'),
            'admin_amount' => array('The admin amount must not be greater than 100 characters.'),
            'admin_amount_deduction_0' => array('The admin amount deduction 0 must not be greater than 100 characters.'),
            'admin_amount_deduction_1' => array('The admin amount deduction 1 must not be greater than 100 characters.'),
            'admin_amount_deduction_2' => array('The admin amount deduction 2 must not be greater than 100 characters.'),
            'table_arch' => array('The table arch must not be greater than 50 characters.'),
            'bank_group_by' => array('The bank group by must not be greater than 50 characters.'),
            'central_group_by' => array('The central group by must not be greater than 50 characters.'),
            'terminal_group_by' => array('The terminal group by must not be greater than 50 characters.'),
            'modified_by' => array('The modified by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPutAsJson('/product/update/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function updateDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'group_name' => '',
            'alias_name' => '',
            'description' => '',
            'table' => '',
            'criteria' => '',
            'find_criteria' => '',
            'bank_criteria' => '',
            'central_criteria' => '',
            'bank' => '',
            'central' => '',
            'terminal' => '',
            'subid' => '',
            'subname'=> '',
            'switch_refnum' => '',
            'switch_payment_refnum' => '',
            'type_transaction' => '-',
            'date' => '',
            'nrek' => '',
            'nbill' => '',
            'bill_amount' => '',
            'admin_amount' => '',
            'admin_amount_deduction_0' => '',
            'admin_amount_deduction_1' => '',
            'admin_amount_deduction_2' => '',
            'table_arch' => '',
            'bank_group_by' => '',
            'central_group_by' => '',
            'terminal_group_by' => '',
            'modified_by' => ''
        ];
        $response_body = [
            'group_name' => array('The group name field is required.'),
                'alias_name' => array('The alias name field is required.'),
                'description' => array('The description field is required.'),
                'table' => array('The table field is required.'),
                'criteria' => array('The criteria field is required.'),
                'find_criteria' => array('The find criteria field is required.'),
                'bank_criteria' => array('The bank criteria field is required.'),
                'central_criteria' => array('The central criteria field is required.'),
                'bank' => array('The bank field is required.'),
                'central' => array('The central field is required.'),
                'terminal' => array('The terminal field is required.'),
                'subid' => array('The subid field is required.'),
                'subname' => array('The subname field is required.'),
                'date' => array('The date field is required.'),
                'nrek' => array('The nrek field is required.'),
                'nbill' => array('The nbill field is required.'),
                'bill_amount' => array('The bill amount field is required.'),
                'admin_amount' => array('The admin amount field is required.'),
                'admin_amount_deduction_0' => array('The admin amount deduction 0 field is required.'),
                'admin_amount_deduction_1' => array('The admin amount deduction 1 field is required.'),
                'admin_amount_deduction_2' => array('The admin amount deduction 2 field is required.'),
                'table_arch' => array('The table arch field is required.'),
                'bank_group_by' => array('The bank group by field is required.'),
                'central_group_by' => array('The central group by field is required.'),
                'terminal_group_by' => array('The terminal group by field is required.'),
                'modified_by' => array('The modified by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPutAsJson('/product/update/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Update Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataSuccess(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPutAsJson('/product/delete/dummy', [
                    'deleted_by' => 'Tegar'
                ]);
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product/Area Success']);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataNotFound(ApiTester $I)
    {
        $response = $I->sendPutAsJson('/product/delete/dummy-123-456', [
            'deleted_by' => 'Tegar'
        ]);
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            $I->seeResponseContainsJson(['result_message' => 'Delete Data Product/Area Failed']);
        } else {
            $I->seeResponseCodeIs(404);
        }
    }

    public function deleteDataInvalidLength(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => 'dummy123457890213456789021346789081234567890123467890123467890123467890123467890
            123456789031245678901324567890123456789012346789021345678903124678902134678902134678901234567890'
        ];
        $response_body = [
            'deleted_by' => array('The deleted by must not be greater than 50 characters.')
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPutAsJson('/product/delete/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataInvalidMandatory(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $data_invalid = [
            'deleted_by' => ''
        ];
        $response_body = [
            'deleted_by' => array('The deleted by field is required.')
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $I->sendDelete('/product/delete/dummy');
            $loop = 1;
        } else if ($count == 0) {
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(400);
        }

        switch ($loop) {
            case 1:
                $I->sendPost('/product/add', $data);
                $response = $I->sendPutAsJson('/product/delete/dummy', $data_invalid);
                if ($response['result_code'] == 400) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(400);
                    $I->seeResponseContainsJson(['result_data' => $response_body]);
                    $I->seeResponseMatchesJSONType([
                        'result_data' => 'array'
                    ]);
                    $I->sendDelete('/product/delete/dummy');
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Data Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(400);
                }
                break;
        }
    }

    public function deleteDataPermanentSuccess(ApiTester $I)
    {
        $data = [
            'name' => 'dummy',
            'group_name' => 'dummy',
            'alias_name' => 'dummy',
            'description' => 'dummy',
            'table' => 'dummy',
            'criteria' => 'dummy',
            'find_criteria' => 'dummy',
            'bank_criteria' => 'dummy', 
            'central_criteria' => 'dummy', 
            'bank' => 'dummy', 
            'central' => 'dummy', 
            'terminal' => 'dummy',
            'subid' => 'dummy',
            'subname'=> 'dummy', 
            'switch_refnum' => 'dummy',
            'switch_payment_refnum' => 'dummy',
            'type_transaction' => '-', 
            'date' => 'dummy', 
            'nrek' => 'dummy', 
            'nbill' => 'dummy', 
            'bill_amount' => 'dummy', 
            'admin_amount' => 'dummy', 
            'admin_amount_deduction_0' => 'dummy', 
            'admin_amount_deduction_1' => 'dummy', 
            'admin_amount_deduction_2' => 'dummy', 
            'table_arch' => 'dummy', 
            'bank_group_by' => 'dummy', 
            'central_group_by' => 'dummy', 
            'terminal_group_by' => 'dummy',
            'created_by' => 'Tegar'
        ];
        $count = $I->grabNumRecords('CSCCORE_TRANSACTION_DEFINITION', ['CSC_TD_NAME' => 'dummy']);
        if ($count == 1) {
            $loop = 1;
        } else if ($count == 0) {
            $I->sendPost('/product/add', $data);
            $loop = 1;
        } else {
            $I->seeResponseCodeIs(200);
        }

        switch ($loop) {
            case 1:
                $response = $I->sendDeleteAsJson('/product/delete/dummy');
                if ($response['result_code'] == 200) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(200);
                    $I->seeResponseContainsJson(['result_message' => 'Delete Product/Area Success']);
                } else if ($response['result_code'] == 500) {
                    $I->seeResponseIsJson();
                    $I->seeResponseCodeIs(500);
                    // $I->seeResponseContainsJson(['result_message' => 'Delete Product/Area Failed']);
                } else {
                    $I->seeResponseCodeIs(200);
                }
                break;
        }
    }

    public function deleteDataPermanentNotFound(ApiTester $I)
    {
        $response = $I->sendDeleteAsJson('/product/delete/dummy-123-456');
        if ($response['result_code'] == 404) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(404);
            $I->seeResponseContainsJson(['result_message' => 'Data Product/Area Not Found']);
        } else if ($response['result_code'] == 500) {
            $I->seeResponseIsJson();
            $I->seeResponseCodeIs(500);
            // $I->seeResponseContainsJson(['result_message' => 'Delete Product/Area Failed']);
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
