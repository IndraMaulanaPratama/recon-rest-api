<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreDownCentral;
use App\Models\CoreProfileFee;
use App\Traits\DownCentralTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoreDownCentralController extends Controller
{
    use ResponseHandler;
    use DownCentralTraits;

    public function getClientId($bearer)
    {
        $bearer_value = substr($bearer, 7);
        $clientId = DB::connection('recon_auth')
        ->table('CSCMOD_CLIENT')
        ->where('csm_c_bearer', $bearer_value)->pluck('csm_c_id');

        return $clientId[0];
    }

    public function getField()
    {
        return [
            'CSC_DC_ID AS ID',
            'CSC_DC_PROFILE AS PROFILE',
            'CSC_DC_NAME AS NAME',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_DC_ID AS ID',
            'CSC_DC_PROFILE AS PROFILE',
            'CSC_DC_NAME AS NAME',
            'CSC_DC_ADDRESS AS ADDRESS',
            'CSC_DC_PHONE AS PHONE',
            'CSC_DC_PIC_NAME AS PIC_NAME',
            'CSC_DC_PIC_PHONE AS PIC_PHONE',
            'CSC_DC_TYPE AS TYPE',
            'CSC_DC_FUND_TYPE AS FUND_TYPE',
            'CSC_DC_TERMINAL_TYPE AS TERMINAL_TYPE',
            'CSC_DC_REGISTERED AS REGISTERED',
            'CSC_DC_ISBLOCKED AS ISBLOCKED',
            'CSC_DC_MINIMAL_DEPOSIT AS MINIMAL_DEPOSIT',
            'CSC_DC_SHORT_ID AS SHORT_ID',
            'CSC_DC_ISBLOCKED AS ISBLOCKED',
            'CSC_DC_COUNTER_CODE AS COUNTER_CODE',
            'CSC_DC_A_ID AS A_ID',
            'CSC_DC_ALIAS_NAME AS ALIAS_NAME',
            'CSC_DC_CREATED_DT AS CREATED',
            'CSC_DC_MODIFIED_DT AS MODIFIED',
            'CSC_DC_CREATED_BY AS CREATED_BY',
            'CSC_DC_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        // Logic Ketika Config Simple
        if ('simple' === $config) {
            $downCentral = CoreDownCentral::getData()->get(self::getField());

            // Response Sukses Get Data CID
            if (null != count($downCentral)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List CID Success',
                    $config,
                    $downCentral
                );
            }

            // Response CID Not Found
            if (null == count($downCentral)) {
                return $this->generalResponse(
                    404,
                    'Data CID Not Found'
                );
            }

            // Response Ketika Gagal Get Data CID
            if (!$downCentral) {
                return $this->generalResponse(
                    500,
                    'Get List CID Success'
                );
            }

        // Logic Ketika Config Detail
        } elseif ('detail' == $config) {
            // Inisialisasi Variable yang Dibutuhkan
            $items = (null != $request->items) ? $request->items : 10;
            $name = [];

            // Get Data Paginasi CID
            $downCentral = CoreDownCentral::getData()
            ->paginate(
                $items,
                $column = self::getPaginate(),
            );

            // *** Logic Menambahkan Profile Name ***
            $countCid = count($downCentral);
            for ($i=0; $i < $countCid; $i++) {
                // Inisialisasi Variable yang Dibutuhkan
                $dataCid[$i] = $downCentral[$i];
                $cidProfile[$i] = $dataCid[$i]->PROFILE;

                // Get Data Profile Name Berdasarkan CID Profile
                $dataProfile[$i] = CoreProfileFee::searchData($cidProfile[$i])
                ->first('CSC_PROFILE_NAME AS PROFILE_NAME');

                // Handle Profile Name kosong di data CID
                if (null == $dataProfile[$i]) {
                    $name[$i] = ["PROFILE_NAME" => null];

                // Simpan Data Profile yang di temukan
                } else {
                    $name[$i] = $dataProfile[$i];
                }

                // Custom Collection Data Paginate CID
                $downCentral[$i] = collect($downCentral[$i]);
                $downCentral[$i]->put("PROFILE", $downCentral[$i]["PROFILE"]. ' - '. $name[$i]["PROFILE_NAME"]);
            }
            // *** End Of Logic Menambahkan Profile Name ***

            // Response Sukses
            if (null != count($downCentral)) {
                // Add Index Number
                $downCentral = $this->addIndexNumber($downCentral);

                return $this->generalConfigResponse(
                    200,
                    'Get List CID Success',
                    $config,
                    $downCentral
                );
            }

            // Response CID Not Found
            if (null == count($downCentral)) {
                return $this->generalResponse(
                    404,
                    'Data CID Not Found'
                );
            }

            // Response Gagal Get CID
            if (!$downCentral) {
                return $this->generalResponse(
                    500,
                    'Get List CID Failed'
                );
            }
        }

        // Response Handler Invalid Config
        return response(
            new ResponseResource(404, 'Page Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function store(Request $request)
    {
        // Handle CID Exists
        $cid = CoreDownCentral::searchData($request->id)->first();
        if (null != $cid) {
            return response()->json(
                new ResponseResource(
                    409,
                    'Data CID Exists'
                ),
                409
            );
        }

        // Handle Data dengan Status Deleted
        $statusDeleted = CoreDownCentral::searchTrashData($request->id)->first(self::getField());
        if (null != $statusDeleted) {
            return response()->json(
                new ResponseResource(
                    422,
                    'Unprocessable Entity'
                ),
                422
            );
        }

        // Logic Add CID
        try {
            // Validation Data Mandatory
            $request->validate([
                'created_by' => ['required', 'string', 'max:50'],
                'id' => ['required', 'max:7'],
                'profile' => ['required', 'max:50'],
                'name' => ['max:100'],
                'address' => ['max:255'],
                'phone' => ['max:50'],
                'pic_name' => ['max:100'],
                'pic_phone' => ['max:100'],
                'type' => ['integer', 'digits_between:1,2'],
                'fund_type' => ['integer', 'digits_between:1,1'],
                'terminal_type' => ['max:4'],
                'minimal_deposit' => ['integer', 'digits_between:1,10'],
                'short_id' => ['max:3'],
                'counter_code' => ['string', 'max:1'],
                'alias_id' => ['max:16'],
                'alias_name' => ['max:255'],
            ]);

            // Proses Add CID
            $clientId = $request->created_by;
            $store = CoreDownCentral::create([
                'CSC_DC_ID' => $request->id,
                'CSC_DC_PROFILE' => $request->profile,
                'CSC_DC_NAME' => $request->name,
                'CSC_DC_ADDRESS' => $request->address,
                'CSC_DC_PHONE' => $request->phone,
                'CSC_DC_PIC_NAME' => $request->pic_name,
                'CSC_DC_PIC_PHONE' => $request->pic_phone,
                'CSC_DC_PIC_TYP' => $request->type,
                'CSC_DC_PIC_FUND_TYPE' => $request->fund_type,
                'CSC_DC_TERMINAL_TYPE' => $request->terminal_type,
                'CSC_DC_MINIMAL_DEPOSIT' => $request->minimal_deposit,
                'CSC_DC_SHORT_ID' => $request->short_id,
                'CSC_DC_ALIAS_NAME' => $request->alias_name,

                'CSC_DC_COUNTER_CODE' => $request->counter_code,
                'CSC_DC_A_ID' => $request->alias_id,
                'CSC_DC_REGISTERED' => Carbon::now('Asia/Jakarta'),
                'CSC_DC_CREATED_BY' => $clientId,
                'CSC_DC_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            ]);

            // Response Failed Proses
            if (!$store) {
                return response(
                    new ResponseResource(500, 'Insert Data CID Failed'),
                    500,
                    ['Accept' => 'Application/json']
                );
            }

            // Response Success Proses
            return response(
                new ResponseResource(200, 'Insert Data CID Success'),
                200,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
            );
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:7']]);

            $data = CoreDownCentral::searchData($request->id)->first(self::getPaginate());

            if (null != $data) {
                return response(new DataResponseResource(200, 'Get Data CID Success', $data), 200);
            }

            return response(new ResponseResource(404, 'Data CID Not Found'), 404);
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()), 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'profile' => ['required', 'string', 'max:50'],
                'name' => ['max:100'],
                'address' => ['max:255'],
                'phone' => ['max:50'],
                'pic_name' => ['max:100'],
                'pic_phone' => ['max:100'],
                'type' => ['integer', 'digits_between:1,2'],
                'fund_type' => ['integer', 'digits:1'],
                'terminal_type' => ['max:4'],
                'counter_code' => ['max:1'],
                'alias_id' => ['max:16'],
                'minimal_deposit' => ['integer', 'digits_between:1,10'],
                'short_id' => ['max:3'],
                'alias_name' => ['max:255'],
            ]);

            $update = CoreDownCentral::searchData($id)->first();
            $clientId = $request->modified_by;

            if (null != $update) {
                $update->CSC_DC_PROFILE = $request->profile;
                $update->CSC_DC_NAME = $request->name;
                $update->CSC_DC_ADDRESS = $request->address;
                $update->CSC_DC_PHONE = $request->phone;
                $update->CSC_DC_PIC_NAME = $request->pic_name;
                $update->CSC_DC_PIC_PHONE = $request->pic_phone;
                $update->CSC_DC_TYPE = $request->type;
                $update->CSC_DC_FUND_TYPE = $request->fund_type;
                $update->CSC_DC_TERMINAL_TYPE = $request->terminal_type;
                $update->CSC_DC_COUNTER_CODE = $request->counter_code;
                $update->CSC_DC_MINIMAL_DEPOSIT = $request->minimal_deposit;
                $update->CSC_DC_SHORT_ID = $request->short_id;
                $update->CSC_DC_ALIAS_NAME = $request->alias_name;
                $update->CSC_DC_MODIFIED_BY = $clientId;
                $update->CSC_DC_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $update->save();

                if ($update) {
                    return response(
                        new ResponseResource(200, 'Update Data CID Success'),
                        200,
                        ['Accept' => 'application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Update Data CID Failed', $update),
                    500,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data CID Not Found'),
                404,
                ['Accept' => 'application/json']
            );
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        if (null != $id) {
            try {
                if (Str::length($id) > 7) {
                    $id = ['The id must not be greater than 7 characters.'];

                    return response(
                        new DataResponseResource(400, 'Invalid Data Validation', $id),
                        400,
                        ['Accept' => 'application/json']
                    );
                }

                $data = CoreDownCentral::searchData($id)->first();

                if (null != $data) {
                    $request->validate(
                        [
                            'deleted_by' => ['required', 'string', 'max:50'],
                        ]
                    );

                    $clientId = $request->deleted_by;
                    $data->CSC_DC_DELETED_BY = $clientId;
                    $data->CSC_DC_DELETED_DT = Carbon::now('Asia/Jakarta');
                    $data->save();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data CID Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Update Data CID Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data CID Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
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
        } else {
            return response()->json(
                new ResponseResource(
                    400,
                    'Invalid Data Validation'
                ),
                400
            );
        }
    }

    public function filter(Request $request)
    {
        // Logic Filter Data CID
        $cid = CoreDownCentral::getData()
        ->where(function ($query) use ($request) {
            if (null != $request->id) {
                $query->where('CSC_DC_ID', 'LIKE', '%' .$request->id.'%');
            }

            if (null != $request->name) {
                $query->where('CSC_DC_NAME', 'LIKE', '%' .$request->name.'%');
            }

            if (null != $request->profile) {
                $query->where('CSC_DC_PROFILE', 'LIKE', '%' .$request->profile.'%');
            }

            if (null != $request->type) {
                $query->where('CSC_DC_TYPE', $request->type);
            }

            if (null != $request->fund_type) {
                $query->where('CSC_DC_FUND_TYPE', 'LIKE', '%' .$request->fund_type.'%');
            }

            if (null != $request->terminal_type) {
                $query->where('CSC_DC_TERMINAL_TYPE', $request->terminal_type);
            }
        })->paginate(
            $perPage = (null != $request->items) ? $request->items : 10,
            $column = self::getPaginate(),
        );

        // *** Logic Menambahkan Profile Name ***
        $countCid = count($cid);
        for ($i=0; $i < $countCid; $i++) {
            // Inisialisasi Variable yang Dibutuhkan
            $dataCid[$i] = $cid[$i];
            $cidProfile[$i] = $dataCid[$i]->PROFILE;

            // Get Data Profile Name Berdasarkan CID Profile
            $dataProfile[$i] = CoreProfileFee::searchData($cidProfile[$i])
            ->first('CSC_PROFILE_NAME AS PROFILE_NAME');

            // Handle Profile Name kosong di data CID
            if (null == $dataProfile[$i]) {
                $name[$i] = ["PROFILE_NAME" => null];

            // Simpan Data Profile yang di temukan
            } else {
                $name[$i] = $dataProfile[$i];
            }

            // Custom Collection Data Paginate CID
            $cid[$i] = collect($cid[$i]);
            $cid[$i]->put("PROFILE", $cid[$i]["PROFILE"]. ' - '. $name[$i]["PROFILE_NAME"]);
        }
        // *** End Of Logic Menambahkan Profile Name ***

        // Response Filter Not Found
        if (null == count($cid)) {
            return $this->generalResponse(
                404,
                'Filter Data CID Not Found'
            );
        }

        // Response Filter Sukses
        if (null != count($cid)) {
            // Add Index Number
            $cid = $this->addIndexNumber($cid);
            return $this->generalDataResponse(
                200,
                'Filter Data CID Success',
                $cid
            );
        }

        // Response Filter Failed
        if (!$cid) {
            return $this->generalResponse(
                500,
                'Filter Data CID Filed',
            );
        }
    }

    public function trash(Request $request)
    {
        // Inisialisasi Variable yang dibutuhkan
        $id = $request->id;
        $name = $request->name;
        $profile = $request->profile;
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data Trash CID
        $trash = CoreDownCentral::getTrashData()
        ->where(function ($query) use ($request) {
            if (null != $request->id) {
                $query->where('CSC_DC_ID', 'LIKE', '%' .$request->id.'%');
            }

            if (null != $request->name) {
                $query->where('CSC_DC_NAME', 'LIKE', '%' .$request->name.'%');
            }

            if (null != $request->profile) {
                $query->where('CSC_DC_PROFILE', 'LIKE', '%' .$request->profile.'%');
            }

            if (null != $request->type) {
                $query->where('CSC_DC_TYPE', $request->type);
            }

            if (null != $request->fund_type) {
                $query->where('CSC_DC_FUND_TYPE', 'LIKE', '%' .$request->fund_type.'%');
            }

            if (null != $request->terminal_type) {
                $query->where('CSC_DC_TERMINAL_TYPE', $request->terminal_type);
            }
        })
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Response Sukses
        if (null != count($trash)) {
            // Add Index Number
            $trash = $this->addIndexNumber($trash);

            return $this->generalDataResponse(
                200,
                'Get Data Trash CID Success',
                $trash
            );
        }

        // Response Not Found
        if (null == count($trash)) {
            return $this->generalResponse(
                404,
                'Get Data Trash CID Not Found'
            );
        }

        // Response Failed
        return $this->generalResponse(
            500,
            'Get Data Trash CID Failed'
        );
    }

    public function dataProfile(Request $request)
    {
        try {
            $message = [
                'cid.digits_between' => 'The cid must not be greater than 7 characters.'
            ];

            $request->validate(['cid' => ['required', 'numeric', 'digits_between:1,7']], $message);
            $cid = $request->cid;

            $cekCid = CoreDownCentral::searchData($cid)->first('CSC_DC_PROFILE AS DC_PROFILE');
            if ($cekCid == null) {
                return response(
                    new ResponseResource(404, 'Data CID Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $dataProfile = CoreDownCentral::searchData($cid)->first('CSC_DC_PROFILE AS ID');
            $dataProfile = CoreProfileFee::searchData($dataProfile->ID)->first('CSC_PROFILE_NAME AS PROFILE_NAME');

            if ($dataProfile == null) {
                return response(
                    new ResponseResource(404, 'Data Profile-CID Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            } elseif ($dataProfile != null) {
                $response['CID'] = $cid;
                $response['PROFILE'] = $dataProfile->PROFILE_NAME;

                return response(
                    new DataResponseResource(200, 'Get Data Profile-CID Success', $response),
                    200,
                    ['Accept' => 'Application/json']
                );
            } else {
                return response(new DataResponseResource(500, 'Get Data Profile Failed', $dataProfile));
            }
        } catch (ValidationException $th) {
            return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()), 400);
        }
    }

    public function unmappingProfile(Request $request)
    {
        // Inisialisasi Variable yang dibutuhkan
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data Unmapping
        $data = CoreDownCentral::getData()
        ->whereNull('CSC_DC_PROFILE')
        ->orWhere('CSC_DC_PROFILE', '')
        ->paginate(
            $perPage = $items,
            $column = [
                'CSC_DC_ID AS CID',
                'CSC_DC_NAME AS CID_NAME',
            ],
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Get List Unmapping Profile-CID Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Unmapping Profile-CID Not Found'
            );
        }

        // Reesponse Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Data Unmapping Profile-CID Failed'
            );
        }
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'profile' => ['required', 'string', 'max:50'],
            ]);

            $cekCid = CoreDownCentral::searchData($id)->first('CSC_DC_PROFILE AS DC_PROFILE');
            if ($cekCid == null) {
                return response(
                    new ResponseResource(404, 'Data CID Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $cekProfileFee = CoreProfileFee::searchData($request->profile)->first('CSC_PROFILE_ID');
            if ($cekProfileFee == null) {
                return response(
                    new ResponseResource(404, 'Data Profile Fee Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $update = CoreDownCentral::searchData($id)->first();
            $clientId = $request->modified_by;

            if (null != $update) {
                $update->CSC_DC_PROFILE = $request->profile;
                $update->CSC_DC_MODIFIED_BY = $clientId;
                $update->CSC_DC_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $update->save();

                if ($update) {
                    return response(
                        new ResponseResource(200, 'Update Data Profile on CID Success'),
                        200,
                        ['Accept' => 'application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Update Data Profile on CID Failed', $update),
                    500,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Profile on CID Not Found'),
                404,
                ['Accept' => 'application/json']
            );
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
            );
        }
    }

    public function manyUpdateProfile(Request $request)
    {
        try {
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'cid' => ['required'],
                'cid.*' => ['numeric', 'digits_between:1,7'],
                'profile' => ['required', 'numeric', 'digits_between:1,50'],
            ]);

            $countCid = count($request->cid);
            $cid = $request->cid;
            $profile = $request->profile;

            $cekProfile = CoreProfileFee::searchData($profile)->first();
            if (null == $cekProfile) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Profile Not Found'
                    ),
                    404
                );
            }

            $clientId = $request->modified_by;
            $cekCid = [];
            $warningCid = [];
            for ($i=0; $i < $countCid; $i++) {
                $cekCid[$i] = CoreDownCentral::searchData($cid[$i])->first();

                if (null == $cekCid[$i]) {
                    $warningCid[] = $cid[$i];
                    unset($cid[$i]);
                } else {
                    $cekCid[$i]->CSC_DC_PROFILE = $request->profile;
                    $cekCid[$i]->CSC_DC_MODIFIED_BY = $clientId;
                    $cekCid[$i]->CSC_DC_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                    $cekCid[$i]->save();
                }
            }

            if (null == $warningCid && $cekProfile != null) {
                return response()->json(
                    new ResponseResource(
                        200,
                        'Update Data Profile with many CID Success'
                    ),
                    200
                );
            } elseif (null != $cekCid) {
                $response = [
                    'cid_not_registered' => $warningCid,
                ];

                return $this->generalDataResponse(
                    202,
                    'Update Data Profile with many CID Success but Some CID Not Registered',
                    $response
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Update Data Profile with many CID Failed',
                    ),
                    500
                );
            }
        } catch (ValidationException $th) {
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
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
            $data = CoreDownCentral::where('CSC_DC_ID', $id)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data CID Not Found'
                    ),
                    404
                );
            }

            CoreDownCentral::where('CSC_DC_ID', $id)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete CID Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete CID Failed',
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
                'cid' => ['required', 'array'],
                'cid.*' => ['string', 'max:7'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->cid;
        $count = count($id);
        $notFound = [];

        // Check Data CID/Down Central
        for ($i=0; $i < $count; $i++) {
            $checkCid = $this->cidTrashData($id[$i]);

            // Validasi CID Not Found
            if (false == $checkCid) :
                $notFound[] = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting & Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response Data CID Not Found
        if (null == $count) :
            return $this->cidNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) {
                $data = CoreDownCentral::searchTrashData($id)->first();
                $data->CSC_DC_DELETED_DT = null;
                $data->CSC_DC_DELETED_BY = null;
                $data->save();
            }
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data CID Failed', $th->getMessage());
        }

        // Response Success
        if (null == $notFound) :
            return $this->generalResponse(200, 'Restore Data CID Success');
        endif;

        // Response Success With Warning
        if (null != $notFound) :
            $response['cid'] = $notFound;
            return $this->generalDataResponse(202, 'Restore Data CID Success But Some Data Not Found', $response);
        endif;
    }
}
