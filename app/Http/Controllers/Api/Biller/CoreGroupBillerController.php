<?php

namespace App\Http\Controllers\Api\Biller;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreBillerCollection;
use App\Models\CoreGroupBiller;
use App\Traits\GroupBillerTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CoreGroupBillerController extends Controller
{
    use GroupBillerTraits;

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
            'CSC_GB_ID AS ID',
            'CSC_GB_NAME AS NAME',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_GB_ID AS ID',
            'CSC_GB_NAME AS NAME',
            'CSC_GB_CREATED_DT AS CREATED',
            'CSC_GB_MODIFIED_DT AS MODIFIED',
            'CSC_GB_CREATED_BY AS  CREATED_BY',
            'CSC_GB_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        if ('simple' == $config) {
            $data = CoreGroupBiller::getData()->get(self::getField());

            if (null != count($data)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Group Biller Success', $config, $data),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Group Biller Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } elseif ('detail' == $config) {
            $items = (null != $request->items) ? $request->items : 10;

            $data = CoreGroupBiller::getData()->paginate(
                $perpage = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);


            if (null != count($data)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Group Biller Success', $config, $data),
                    200,
                    ['Accept' => 'application/json']
                );
            }
            if (null == count($data)) {
                return response(
                    new ResponseResource(404, 'Data Group Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(500, 'Get List Data Group Biller Failed'),
                500,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Group Biller Not Found'),
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
                    'id' => ['required', 'string', 'max:20'],
                    'name' => ['required', 'string', 'max:100'],
                ]
            );

            $clientId = $request->created_by;
            $cekData = CoreGroupBiller::searchData($request->id, $request->name)->first(self::getField());
            $statusDeleted = CoreGroupBiller::searchTrashData($request->id)->first(self::getField());

            if (null == $statusDeleted) {
                if (null == $cekData) {
                    $store = CoreGroupBiller::create([
                        'CSC_GB_ID' => $request->id,
                        'CSC_GB_NAME' => $request->name,
                        'CSC_GB_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                        'CSC_GB_CREATED_BY' => $clientId,
                    ]);

                    if ($store) {
                        return response(new ResponseResource(
                            200,
                            'Insert Data Group Biller Success'
                        ), 200, ['Accept' => 'Application/json']);
                    }

                    return response(
                        new DataResponseResource(
                            500,
                            'Insert Data Group Biller Failed',
                            $store
                        ),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(409, 'Data Group Biller Exists'),
                    409,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(422, 'Unprocessable Entity'),
                422,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:20']]);

            $data = CoreGroupBiller::searchData($request->id)->first(self::getPaginate());

            if (null != $data) {
                return response(
                    new DataResponseResource(200, 'Get Data Group Biller Success', $data),
                    200,
                    ['Accept' => 'Application/json']
                );
            }
            if (null == $data) {
                return response(
                    new ResponseResource(404, 'Data Group Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(500, 'Get Data Data Group Biller Failed'),
                500,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:100'],
            ]);

            $data = CoreGroupBiller::searchData($id)->first();
            $clientId = $request->modified_by;

            if (null != $data) {
                $data->CSC_GB_NAME = $request->name;
                $data->CSC_GB_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $data->CSC_GB_MODIFIED_BY = $clientId;
                $data->save();

                if ($data) {
                    return response(
                        new ResponseResource(200, 'Update Data Group Biller Success'),
                        200,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Update Data Group Biller Failed', $data),
                    500,
                    ['Accept' => 'Application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Group Biller Not Found'),
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
                if (Str::length($id) > 20) {
                    $id = ['The id must not be greater than 20 characters.'];

                    return response(
                        new DataResponseResource(400, 'Invalid Data Validation', $id),
                        400,
                        ['Accept' => 'application/json']
                    );
                }

                $request->validate(
                    ['deleted_by' => ['required', 'string', 'max:50']]
                );

                $clientId = $request->deleted_by;
                $data = CoreGroupBiller::searchData($id)->first();

                if (null != $data) {
                    $data->CSC_GB_DELETED_BY = $clientId;
                    $data->CSC_GB_DELETED_DT = Carbon::now('Asia/Jakarta');
                    $data->save();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Group Biller Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Delete Data Group Biller Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Group Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } catch (ValidationException $th) {
                return $this->invalidValidation($th->validator->errors());
            }
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }

    public function filter(Request $request)
    {
        $items = (null != $request->items) ? $request->items : 10;
        $data = CoreGroupBiller::getData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_GB_NAME', 'LIKE', '%'. $request->name.'%');
            }
        })

        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);


        if (null != count($data)) {
            return response(
                new DataResponseResource(200, 'Filter Data Group Biller Success', $data),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Filter Data Group Biller Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function trash(Request $request)
    {
        $items = (null != $request->items) ? $request->items : 10;
        $data = CoreGroupBiller::getTrashData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_GB_NAME', 'LIKE', '%'. $request->name.'%');
            }
        })

        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);


        if (null != count($data)) {
            return response(
                new DataResponseResource(200, 'Get Trash Data Group Biller Success', $data),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Trash Group Biller Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function listBiller(Request $request)
    {
        try {
            $request->validate(['group_biller' => ['required', 'string', 'max:20']]);

            $cekGroupBiller = CoreGroupBiller::searchData($request->group_biller)->first('CSC_GB_ID');
            if ($cekGroupBiller == null) {
                return response()->json(
                    new ResponseResource(404, 'Data Group Biller Not Found'),
                    404
                );
            }

            $items = $request->items;
            $items = ($items == null) ? 10 : $items;
            $data = CoreBillerCollection::join(
                'CSCCORE_GROUP_BILLER AS GB',
                'GB.CSC_GB_ID',
                '=',
                'CSC_BC_GROUP_BILLER'
            )
            ->join(
                'CSCCORE_BILLER AS BILLER',
                'BILLER.CSC_BILLER_ID',
                '=',
                'CSC_BC_BILLER'
            )
            ->where('GB.CSC_GB_ID', $request->group_biller)
            ->paginate(
                $items = $items,
                $column = [
                    'CSC_BC_ID AS ID',
                    'CSC_BC_BILLER AS BILLER_ID',
                    'BILLER.CSC_BILLER_GROUP_PRODUCT AS GOP',
                    'BILLER.CSC_BILLER_NAME AS BILLER_NAME'
                ]
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null == count($data)) {
                return response()->json(
                    new ResponseResource(404, 'Data Group Biller-Biller Not Found'),
                    404
                );
            } elseif (null != count($data)) {
                return response()->json(
                    new DataResponseResource(200, 'Get List Group Biller-Biller Success', $data),
                    200
                );
            } else {
                return response()->json(
                    new ResponseResource(500, 'Get List Data Group Biller-Biller Failed'),
                    500
                );
            }
        } catch (ValidationException $th) {
            return response()->json(
                new DataResponseResource(
                    400,
                    'Invalid Data Validation',
                    $th->validator->errors()
                ),
                400
            );
        }
    }

    public function listAddBiller(Request $request, $config)
    {
        // Validasi Config
        if ('detail' != $config && 'simple' != $config) :
            return $this->invalidValidation();
        endif;

        // Inisialisasi Variable
        $items = $request->items;
        $items = ($items == null) ? 10 : $items;

        // Logic Detail Config
        if ('detail' == $config) :
            $data = CoreBiller::getData()
            ->whereNotExists(function ($query) {
                $query->select('BC.CSC_BC_BILLER')
                ->from('CSCCORE_BILLER_COLLECTION AS BC')
                ->whereColumn('CSC_BILLER_ID', 'BC.CSC_BC_BILLER');
            })
            ->paginate(
                $items = $items,
                $column = [
                    'CSC_BILLER_ID AS BILLER_ID',
                    'CSC_BILLER_GROUP_PRODUCT AS GOP',
                    'CSC_BILLER_NAME AS BILLER_NAME'
                ]
            );

            // Hitung jumlah data
            $countData = count($data);

            // Add Index Number
            if (null != $countData) :
                $data = $this->addIndexNumber($data);
            endif;
        endif;

        // Logic Simple Config
        if ('simple' == $config) :
            $data = CoreBiller::getData()
            ->whereNotExists(function ($query) {
                $query->select('BC.CSC_BC_BILLER')
                ->from('CSCCORE_BILLER_COLLECTION AS BC')
                ->whereColumn('CSC_BILLER_ID', 'BC.CSC_BC_BILLER');
            })
            ->get(
                [
                    'CSC_BILLER_ID AS BILLER_ID',
                    'CSC_BILLER_GROUP_PRODUCT AS GOP',
                    'CSC_BILLER_NAME AS BILLER_NAME'
                ]
            );

            // Hitung jumlah data
            $countData = count($data);
        endif;

        // Response Not Found
        if (null == $countData) :
            return $this->responseNotFound('Data Group Biller-Biller Not Found');
        endif;

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Get List Add Group Biller-Biller Failed');
        endif;

        // Response Success
        if ($data) :
            return $this->generalDataResponse(200, 'Get List Add Group Biller-Biller Success', $data);
        endif;
    }

    public function addBiller(Request $request)
    {
        try {
            $request->validate(
                [
                    'group_biller' => ['required', 'string', 'max:20'],
                    'biller' => ['required', 'array', 'min:1'],
                    'biller.*' => ['string', 'max:5'],
                ]
            );

            $groupBiller = $request->group_biller;
            $biller = $request->biller;

            $countBiller = count($biller);
            $warningNotRegistered = [];
            $warningExists = [];

            $cekGroupBiller = CoreGroupBiller::searchData($groupBiller)->first('CSC_GB_ID');
            if (null == $cekGroupBiller) {
                return $this->responseNotFound('Data Group Biller Not Found');
            }

            for ($i=0; $i < $countBiller; $i++) {
                $cekBiller[$i] = CoreBiller::searchData($biller[$i])
                ->first('CSC_BILLER_ID');

                $cekExist[$i] = CoreBillerCollection::searchByGroupAndBiller($groupBiller, $biller[$i])
                ->first('CSC_BC_ID');

                // null:$cekbiller = false
                // null:$cekExist = true

                if (null != $cekBiller[$i] && null != $cekExist[$i]) {
                    $warningExists[] = $biller[$i];
                    unset($biller[$i]);
                } elseif (null == $cekBiller[$i] && null == $cekExist[$i]) {
                    $warningNotRegistered[] = $biller[$i];
                    unset($biller[$i]);
                } elseif (null != $cekBiller[$i] && null != $cekExist[$i]) {
                    $warningExists[] = $biller[$i];
                    $warningNotRegistered[] = $biller[$i];
                    unset($biller[$i]);
                } else {
                    $keterangan = null;
                    while ($keterangan == false) {
                        $id = Uuid::uuid4();
                        $cekId = CoreBillerCollection::searchData($id)->first();

                        if (null == $cekId) {
                            $keterangan = true;
                        } else {
                            $keterangan = false;
                        }
                    }
                    CoreBillerCollection::create(
                        [
                            'CSC_BC_ID' => $id,
                            'CSC_BC_GROUP_BILLER' => $groupBiller,
                            'CSC_BC_BILLER' => $biller[$i],
                        ]
                    );
                }
            }

            if ($warningExists == null && $warningNotRegistered == null) {
                return response()->json(
                    new ResponseResource(
                        200,
                        'Insert Data Group Biller-Biller Success'
                    ),
                    200
                );
            } elseif (null != $warningExists && null != $warningNotRegistered) {
                $response['biller_exists'] = $warningExists;
                $response['biller_not_registered'] = $warningNotRegistered;

                return $this->generalDataResponse(
                    202,
                    'Insert Data Group Biller-Biller Success but Some Biller Cannot Processed',
                    $response
                );
            } elseif (null != $warningExists) {
                $response['biller_exists'] = $warningExists;

                return $this->generalDataResponse(
                    202,
                    'Insert Data Group Biller-Biller Success but Some Biller Exists',
                    $response
                );
            } elseif (null != $warningNotRegistered) {
                $response['biller_not_registered'] = $warningNotRegistered;

                return $this->generalDataResponse(
                    202,
                    'Insert Data Group Biller-Biller Success but Some Biller Not Registered',
                    $response
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Insert Data Group Biller-Biller Failed'
                    ),
                    500
                );
            }
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }
    }

    public function deleteBiller($id)
    {
        if (null != $id) {
            try {
                if (Str::length($id) > 36) {
                    $id = ['The id must not be greater than 36 characters.'];

                    return response(
                        new DataResponseResource(400, 'Invalid Data Validation', $id),
                        400,
                        ['Accept' => 'application/json']
                    );
                }

                $data = CoreBillerCollection::searchData($id)->first();

                if (null != $data) {
                    $data->delete();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Group Biller-Biller Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new ResponseResource(500, 'Delete Data Group Biller-Biller Success Failed'),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Group Biller-Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } catch (ValidationException $th) {
                return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
            }
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }

    public function deleteData(Request $request, $id)
    {
        if (null == $id) {
            $id = ['id' => 'The id Field Is Required'];
            return $this->invalidValidation($id);
        }

        // Logic Get Data
        $data = CoreGroupBiller::where('CSC_GB_ID', $id)->first();

        // Response Not Found
        if (null == $data) {
            return $this->responseNotFound('Data Group Biller Not Found');
        }

        // Logic Delete Data
        $data->delete();

        // Response Succes
        if ($data) :
            return $this->generalResponse(200, 'Delete Group Biller Success');
        endif;

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Delete Group Biller Faileds');
        endif;
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['string', 'max:20'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $count = count($id);
        $notFound = [];

        // Check Data
        for ($i=0; $i < $count; $i++) {
            $checkAccount = $this->groupBillerSearchDeletedData($id[$i]);

            // Validasi Data
            if (false == $checkAccount) :
                $notFound = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response  Not Found
        if (null == $count) :
            return $this->groupBillerNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->groupBillerSearchDeletedData($id[$n]);
                $data->CSC_GB_DELETED_BY = null;
                $data->CSC_GB_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Group Biller Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Group Biller  Success But Some Data Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Group Biller Success');
    }
}
