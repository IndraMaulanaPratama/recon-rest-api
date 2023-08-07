<?php

namespace App\Http\Controllers\Admin\ApiManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\ApiName;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiNameController extends Controller
{
    public function __construct()
    {
        $this->middleware('clientRegister');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $apiName = ApiName::where('CSM_AN_DELETED_DT', null)->paginate(
            $perpage = (null != $request->items) ? $request->items : 10,
            $column = [
                'CSM_AN_ID AS ID',
                'CSM_AN_DESC AS DESCIPTIONS',
                'CSM_AN_CREATED_DT',
            ],
        );

        return response(
            new DataResponseResource(200, 'List All API Name', $apiName),
            200,
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
                'id' => ['required', 'string'],
            ]);

            $store = ApiName::create([
                'CSM_AN_ID' => $request->id,
                'CSM_AN_DESC' => $request->description,
                'CSM_AN_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSM_AN_DELETED_DT' => null,
            ]);

            if ($store) {
                return response(
                    new ResponseResource(200, 'API Name has been created'),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new DataResponseResource(500, 'Failed Created API Name', $store),
                500,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Credentials', $th->validator->errors()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $apiName = ApiName::where(['CSM_AN_ID' => $request->id, 'CSM_AN_DELETED_DT' => null])->first([
            'CSM_AN_ID AS ID',
            'CSM_AN_DESC AS DESCIPTIONS',
            'CSM_AN_CREATED_DT AS CREATED_AT',
        ]);

        if (null != $apiName) {
            return response(
                new DataResponseResource(200, 'Data API Name', $apiName),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => ['required'],
                'description' => ['string', 'max:255'],
            ]);

            $apiName = ApiName::where(['CSM_AN_ID' => $request->id, 'CSM_AN_DELETED_DT' => null])->first();

            if (null != $apiName) {
                $apiName->CSM_AN_DESC = $request->description;
                $apiName->save();

                return response(
                    new ResponseResource(200, 'Api Name has been updated'),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Api Name Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Credentials', $th->validator->errors()),
                400,
                ['Accept' => 'Application/json']
            );
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
        $apiName = ApiName::where(['CSM_AN_ID' => $request->id, 'CSM_AN_DELETED_DT' => null])->first();

        if (null != $apiName) {
            $destroy = ApiName::where('CSM_AN_ID', $request->id)
            ->update(['CSM_AN_DELETED_DT' => Carbon::now('Asia/Jakarta')]);

            if ($destroy) {
                return response(
                    new ResponseResource(200, 'API Name has been deleted'),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new DataResponseResource(400, 'Failed Deleted Api Name', $destroy),
                400,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function filter(Request $request)
    {
        $filter = ApiName::where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSM_AN_ID', 'LIKE', $request->name.'%');
            }

            if (1 == $request->isDeleted) {
                $query->whereNotNull('CSM_AN_DELETED_DT');
            } elseif (0 == $request->isDeleted) {
                $query->whereNull('CSM_AN_DELETED_DT');
            } else {
                $query->where('CSM_AN_DELETED_DT', 'PASTI TIDAK ADA');
            }
        })->paginate(
            $perpage = (null != $request->item) ? $request->items : 10,
            $column = [
                'CSM_AN_ID AS NAME',
                'CSM_AN_DELETED_DT AS DESCRIPTIONS',
            ],
        );

        if (null != $filter) {
            return response(
                new DataResponseResource(200, 'List Data Api Name', $filter),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return Response(
            new ResponseResource(404, 'Data Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }
}
