<?php

namespace App\Http\Controllers\Api\Bank;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBank;
use App\Traits\BankTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoreBankController extends Controller
{
    use ResponseHandler;
    use BankTraits;

    public function getClientId($bearer)
    {
        $bearer_value = substr($bearer, 7);
        $clientId = DB::connection('recon_auth')
        ->table('CSCMOD_CLIENT')
        ->where('csm_c_bearer', $bearer_value)
        ->pluck('csm_c_id');

        return $clientId[0];
    }

    public function getField()
    {
        return [
            'CSC_BANK_CODE AS CODE',
            'CSC_BANK_NAME AS NAME',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_BANK_CODE AS CODE',
            'CSC_BANK_NAME AS NAME',
            'CSC_BANK_CREATED_DT AS CREATED',
            'CSC_BANK_MODIFIED_DT AS MODIFIED',
            'CSC_BANK_CREATED_BY AS CREATED_BY',
            'CSC_BANK_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        if ('simple' == $config) {
            $data = CoreBank::getData()->get(self::getField());

            if (null != count($data)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Bank Success', $config, $data),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Bank Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } elseif ('detail' == $config) {
            $items = (null != $request->items) ? $request->items : 10;

            $data = CoreBank::getData()->paginate(
                $perpage = $items,
                $column = self::getPaginate(),
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null != count($data)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Bank Success', $config, $data),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Bank Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Bank Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'created_by' => ['required', 'string', 'max:50'],
                    'code' => ['required', 'numeric', 'digits_between:1,3'],
                    'name' => ['required', 'string', 'max:50'],
                ]
            );

            $statusDeleted = CoreBank::searchTrashData($request->code)->first(self::getField());

            if (null != $statusDeleted) {
                return response(
                    new ResponseResource(422, 'Unprocessable Entity'),
                    422,
                    ['Accept' => 'Application/json']
                );
            }

            $cekData = CoreBank::searchData($request->code)->first(self::getField());
            $clientId = $request->created_by;

            if (null == $cekData) {
                $store = CoreBank::create([
                    'CSC_BANK_CODE' => $request->code,
                    'CSC_BANK_NAME' => $request->name,
                    'CSC_BANK_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                    'CSC_BANK_CREATED_BY' => $clientId,
                ]);

                if ($store) {
                    return response(
                        new ResponseResource(200, 'Insert Data Bank Success'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Insert Data Bank Failed', $store),
                    500,
                    ['Accept' => 'Application/json']
                );
            }

            return response(new ResponseResource(409, 'Data Bank Exists'), 409, ['Accept' => 'Application/json']);
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate(['code' => ['required', 'numeric', 'digits_between:1,3']]);

            $data = CoreBank::searchData($request->code)->first(self::getPaginate());

            if (null != $data) {
                return response(
                    new DataResponseResource(200, 'Get Data Bank Success', $data),
                    200,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Bank Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'modified_by' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:50'],
                ]
            );

            $data = CoreBank::searchData($id)->first();
            $clientId = $request->modified_by;

            if (null != $data) {
                $data->CSC_BANK_NAME = $request->name;
                $data->CSC_BANK_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $data->CSC_BANK_MODIFIED_BY = $clientId;
                $data->save();

                if ($data) {
                    return response(
                        new ResponseResource(200, 'Update Data Bank Success'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Update Data Bank Failed', $data),
                    500,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Bank Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        if (null != $id) {
            try {
                if (Str::length($id) > 3) {
                    $id = ['The id must not be greater than 3 characters.'];
                    return $this->invalidValidation($id);
                }

                $request->validate(
                    ['deleted_by' => ['required', 'string', 'max:50']]
                );

                $clientId = $request->deleted_by;
                $data = CoreBank::searchData($id)->first();

                if (null != $data) {
                    $data->CSC_BANK_DELETED_BY = $clientId;
                    $data->CSC_BANK_DELETED_DT = Carbon::now('Asia/Jakarta');
                    $data->save();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Bank Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Update Data Bank Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Bank Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } catch (ValidationException $th) {
                return $this->invalidValidation($th->validator->errors());
            }
        } else {
            return $this->invalidValidation();
        }
    }

    public function filter(Request $request)
    {
        // Inisialisasi Variable yang dibutuhkan
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Filter Data
        $data = CoreBank::getData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_BANK_NAME', 'LIKE', '%'. $request->name.'%');
            }
        })
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Filter Data Bank Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Bank Not Found'
            );
        }
    }

    public function trash(Request $request)
    {
        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data Trash
        $data = CoreBank::getTrashData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_BANK_NAME', 'LIKE', '%'. $request->name.'%');
            }
        })
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Get Trash Data Bank Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Trash Bank Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get Trash Data Bank Failed',
            );
        }
    }

    public function deleteData(Request $request, $id)
    {
        if (null == $id) {
            $id = ['id' => 'The id Field Is Required'];

            return response()->json(
                new DataResponseResource(
                    400,
                    'Invalid Data Validation',
                    $id
                ),
                400
            );
        }

        try {
            $data = CoreBank::where('CSC_BANK_CODE', $id)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Bank Not Found'
                    ),
                    404
                );
            }

            CoreBank::where('CSC_BANK_CODE', $id)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete Bank Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete Bank Failed',
                    $th
                ),
                500
            );
        }
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['numeric', 'digits_between:1,3'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $count = count($id);
        $notFound = [];

        // Check Data Bank
        for ($i=0; $i < $count; $i++) {
            $checkBank = $this->bankSearchDeletedData($id[$i]);

            // Validasi Data Bank
            if (false == $checkBank) :
                $notFound = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response Bank Not Found
        if (null == $count) :
            return $this->bankNotFound();
        endif;

        // Logic Restore Data Bank
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->bankSearchDeletedData($id[$n]);
                $data->CSC_BANK_DELETED_BY = null;
                $data->CSC_BANK_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Bank Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(202, 'Restore Data Bank Success But Some Data Not Found', $response);
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Bank Success');
    }
}
