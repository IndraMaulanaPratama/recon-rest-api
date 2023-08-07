<?php

namespace App\Http\Controllers\Admin\ApiManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\ApiName;
use App\Models\ApiScope;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class ApiScopeController extends Controller
{
    public function getField()
    {
        return [
            'CSM_CAS_ID AS ID',
            'CSM_CAS_ClIENT AS Client',
            'CSM_CAS_API_NAME AS API_NAME',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $apiScope = ApiScope::paginate(
            $perpage = (null != $request->items) ? $request->items : 10,
            $column = self::getField()
        );

        if ($apiScope) {
            return response(
                new DataResponseResource(200, 'List Api Scope', $apiScope),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new DataResponseResource(500, 'Internal Error', $apiScope),
            500,
            ['Accept' => 'Application/json']
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => ['required', 'string', 'max:50'],
                'api_name' => ['required'],
            ]);

            $cekClient = Client::where('csm_c_id', $request->client_id)->first('csm_c_id AS client_id');

            if (null == $cekClient) {
                return response(
                    new ResponseResource(404, 'Data Client Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $api_name = $request->api_name;
            $countApi = count($api_name);
            $warninRegistered = [];
            $warninExist = [];

            for ($i = 0; $i < $countApi; ++$i) {
                $cekData[$i] = ApiName::where('CSM_AN_ID', $api_name[$i])
                ->first(['CSM_AN_ID AS api_name']); // null is false

                $cekExist[$i] = ApiScope::searchDataByClient($request->client_id, $api_name[$i])
                ->first(); // null is true

                if (null == $cekData[$i] && null == $cekExist[$i]) {
                    $warninRegistered[] = $api_name[$i];
                    unset($api_name[$i]);
                } elseif (null != $cekData[$i] && null != $cekExist[$i]) {
                    $warninExist[] = [
                        'client_id' => $cekExist[$i]->CSM_CAS_CLIENT,
                        'api_name' => $cekExist[$i]->CSM_CAS_API_NAME
                    ];
                    unset($api_name[$i]);
                } elseif (null == $cekData[$i] && null != $cekExist[$i]) {
                    $warninRegistered[] = $api_name[$i];
                    $warninExist[] = [
                        'client_id' => $cekExist[$i]->CSM_CAS_CLIENT,
                        'api_name' => $cekExist[$i]->CSM_CAS_API_NAME
                    ];
                    unset($api_name[$i]);
                } else {
                    ApiScope::create([
                        'CSM_CAS_ID' => Uuid::uuid4(),
                        'CSM_CAS_ClIENT' => $request->client_id,
                        'CSM_CAS_API_NAME' => $api_name[$i],
                    ]);
                }
            }

            if (null == $warninRegistered && null == $warninExist) {
                return response(
                    new ResponseResource(200, 'Data API Scope has been created'),
                    200,
                    ['Accept' => 'Application/json']
                );
            }
            if (null != $warninRegistered && null == $warninExist) {
                $status = 202;

                return response()->json([
                    'result_code' => $status,
                    'result_message' => 'Insert Data API Scope Success but Some Data Not Registered',
                    'api_name_not_registered' => $warninRegistered,
                ], $status, ['Accept' => 'Application/json']);
            }
            if (null == $warninRegistered && null != $warninExist) {
                $status = 202;

                return response()->json([
                    'result_code' => $status,
                    'result_message' => 'Insert Data API Scope Success but Some Data Exists',
                    'api_name_exists' => $warninExist,
                ], $status, ['Accept' => 'Application/json']);
            }
            if (null != $warninRegistered && null != $warninExist) {
                $status = 202;

                return response()->json([
                    'result_code' => $status,
                    'result_message' => 'Insert Data API Scope Success but Some Data Cannot Processed',
                    'api_name_not_registered' => $warninRegistered,
                    'api_name_exists' => $warninExist,
                ], $status, ['Accept' => 'Application/json']);
            }

            return response(
                new ResponseResource(500, 'Internal Server Error'),
                500,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Credentials', $th->validator->errors()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate(['id' => ['required']]);

            $validate = ApiScope::where('CSM_CAS_ID', $request->id)->first();

            if (null != $validate) {
                $destroy = ApiScope::where('CSM_CAS_ID', $request->id)->delete();

                if ($destroy) {
                    return response(
                        new ResponseResource(200, 'API Scope has been deleted'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Internal Error', $destroy),
                    500,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Credentials', $th->validator->errors()));
        }
    }

    public function filter(Request $request)
    {
        $filter = ApiScope::where(function ($query) use ($request) {
            if (null != $request->client_id) {
                $query->where('CSM_CAS_CLIENT', 'LIKE', $request->client_id.'%');
            }

            if ($request->api_name) {
                $query->where('CSM_CAS_API_NAME', 'LIKE', $request->api_name.'%');
            }
        })->paginate(
            $perpage = (null != $request->items) ? $request->name : 10,
            $column = [
                'CSM_CAS_ID AS ID_SCOPE',
                'CSM_CAS_CLIENT AS CLIENT_NAME',
                'CSM_CAS_API_NAME AS API_NAME',
            ],
        );

        if ($filter) {
            return response(
                new DataResponseResource(200, 'List Api Scope', $filter),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new DataResponseResource(500, 'Internal Error', $filter),
            500,
            ['Accept' => 'Appilication/json']
        );
    }
}
