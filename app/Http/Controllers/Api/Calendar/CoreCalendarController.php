<?php

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreCalendar;
use App\Models\CoreCalendarDay;
use App\Traits\CalendarTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use function PHPSTORM_META\map;

class CoreCalendarController extends Controller
{
    use ResponseHandler;
    use CalendarTraits;

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
            'CSC_CAL_ID AS ID',
            'CSC_CAL_NAME AS NAME',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_CAL_ID AS ID',
            'CSC_CAL_NAME AS NAME',
            'CSC_CAL_CREATED_DT AS CREATED',
            'CSC_CAL_MODIFIED_DT AS MODIFIED',
            'CSC_CAL_CREATED_BY AS CREATED_BY',
            'CSC_CAL_MODIFIED_BY AS MODIFIED_BY',
            'CSC_CAL_DEFAULT AS DEFAULT',
        ];
    }

    public function viewCalendar()
    {
        return [
            'CSC_CD_DATE AS DATE',
            'CSC_CD_DESC AS DESC'
        ];
    }

    public function index(Request $request, $config)
    {
        if ('simple' == $config) {
            $data = CoreCalendar::getData()->get(self::getField());

            if (null != count($data)) {
                return response()->json(
                    new PaginateResponseResource(200, 'Get List Calendar Success', $config, $data),
                    200
                );
            }

            return response(
                new ResponseResource(400, 'Data Calendar Not Found'),
                400,
                ['Accept' => 'application/json']
            );
        } elseif ('detail' == $config) {
            $items = (null != $request->items) ? $request->items : 10;

            $data = CoreCalendar::getData()->paginate(
                $perpage = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null != count($data)) {
                return response()->json(
                    new PaginateResponseResource(200, 'Get List Calendar Success', $config, $data),
                    200
                );
            }

            return response(
                new ResponseResource(404, 'Data Calendar Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(500, 'Get List Data Calendar Failed'),
            500,
            ['Accept' => 'Application/json']
        );
    }

    public function view(Request $request)
    {
        try {
            // Validasi Data Mandatory
            $request->validate(
                [
                    'id' => ['required', 'string', 'max:20'],
                ]
            );

            // Inisialisasi Variable yang dibutuhkan
            $id = $request->id;

            // Check Data Calendaer By ID
            $calendar = CoreCalendar::searchData($id)->first();
            if (null == $calendar) {
                return $this->generalResponse(
                    404,
                    'Data Calendar Not Found'
                );
            }

            // Logic Get Detail Calendar
            $detailCalendar = CoreCalendarDay::join(
                'CSCCORE_CALENDAR AS CALENDAR',
                'CALENDAR.CSC_CAL_ID',
                '=',
                'CSC_CD_CALENDAR'
            )
            ->searchCalendar($id)
            ->get(self::viewCalendar());

            // Response Detail Calendar Not Found
            if (null == count($detailCalendar)) {
                return $this->generalResponse(
                    404,
                    'Data Detail Calendar Not Found'
                );
            }

            // Response Detail Calendar Sukses
            if (null != count($detailCalendar)) {
                return $this->generalDataResponse(
                    200,
                    'Get Data Detail Calendar Success',
                    $detailCalendar
                );
            }

            // Response Gagal Proses
            if (!$detailCalendar) {
                return $this->generalResponse(
                    500,
                    'Get Data Detail Calendar Failed'
                );
            }
        } catch (ValidationException $th) {
            // Response Invalid Data Validation
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }
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

            $defaultCalendar = CoreCalendar::searchDefault()->first();
            $clientId = $request->created_by;

            if (true == $defaultCalendar) {
                // return true;
                $field = [
                    'CSC_CAL_ID' => $request->id,
                    'CSC_CAL_NAME' => $request->name,
                    'CSC_CAL_CREATED_BY' => $clientId,
                    'CSC_CAL_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                ];
            } else {
                $field = [
                    'CSC_CAL_ID' => $request->id,
                    'CSC_CAL_NAME' => $request->name,
                    'CSC_CAL_CREATED_BY' => $clientId,
                    'CSC_CAL_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                ];
            }

            $cekData = CoreCalendar::searchData($request->id)->first(self::getField());
            $statusDeleted = CoreCalendar::searchTrashData($request->id)->first(self::getField());

            if (null == $statusDeleted) {
                if (null == $cekData) {
                    $store = CoreCalendar::create($field);

                    if ($store) { // Return Success Insert Data
                        return response(
                            new ResponseResource(200, 'Insert Data Calendar Success'),
                            200,
                            ['Accept' => 'application/json']
                        );
                    }   // Return Internal Server Errors

                    return response(
                        new DataResponseResource(500, 'Isert Data Calendar Failed ', $store),
                        500,
                        ['Accept' => 'application/json']
                    );
                }

                return response(
                    new ResponseResource(409, 'Data Calendar Exists'),
                    409,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(422, 'Unprocessable Entity'),
                422,
                ['Accept' => 'Application/json']
            );
        } catch (ValidationException $th) { // Return error Credentials Or Validation
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
            $request->validate(['id' => ['required', 'string', 'max:20']]);

            $data = CoreCalendar::searchData($request->id)->first(self::getField());

            if (isset($data)) {
                return response(
                    new DataResponseResource(200, 'Get Data Calendar Success', $data),
                    200,
                    ['Accept' => 'application/json']
                );
            }
            if (!isset($data)) {
                return response(
                    new ResponseResource(404, 'Data Calendar Not Found'),
                    404,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(500, 'Data Calendar Failed'),
                500,
                ['Accept' => 'application/json']
            );
        } catch (ValidationException $th) { // Return error Credentials Or Validation
            return response(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'modified_by' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:100'],
                ]
            );

            $cekPartner = CoreCalendar::searchData($id)->first();
            $clientId = $request->modified_by;

            if (null != $cekPartner) {
                $cekPartner->CSC_CAL_NAME = $request->name;
                $cekPartner->CSC_CAL_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $cekPartner->CSC_CAL_MODIFIED_BY = $clientId;
                $cekPartner->save();

                if ($cekPartner) {
                    return response(
                        new ResponseResource(200, 'Update Data Calendar Success'),
                        200,
                        ['Accept' => 'application/json']
                    );
                }

                return response(
                    new DataResponseResource(500, 'Update Data Calendar Failed ', $cekPartner),
                    500,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Calendar Not Found'),
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

    public function setDefault(Request $request, $id)
    {
        if (Str::length($id) >= 20) :
            return $this->invalidValidation($response = ['id' => 'The id must not be greater than 20 characters.']);
        endif;

        $cekCalendar = CoreCalendar::searchData($id)->first();

        if (null != $cekCalendar) {
            $defaultCalendar = CoreCalendar::searchDefault()->first();

            if (null == $defaultCalendar) { // Kondisi ketika belum ada data default
                $cekCalendar->CSC_CAL_DEFAULT = 0;
                $cekCalendar->CSC_CAL_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $cekCalendar->save();

                if ($cekCalendar) {
                    return $this->generalResponse(200, 'Set Default Data Calendar Success');
                } else {
                    return $this->failedResponse('Set Default Data Calendar Failed');
                }
            }

            // Kondisi ketika nilai default di pilih menjadi nilai default
            if ($defaultCalendar->CSC_CAL_DEFAULT == $cekCalendar->CSC_CAL_DEFAULT) {
                return $this->generalResponse(200, 'Set Default Data Calendar Success');
            }   // Kondisi ketika ada data baru yang di jadikan nilai default
            $defaultCalendar->CSC_CAL_DEFAULT = 1;
            $defaultCalendar->CSC_CAL_MODIFIED_DT = Carbon::now('Asia/Jakarta');
            $defaultCalendar->save();

            if ($defaultCalendar) {
                $cekCalendar->CSC_CAL_DEFAULT = 0;
                $cekCalendar->CSC_CAL_MODIFIED_DT = Carbon::now('Asia/Jakarta');
                $cekCalendar->save();

                if ($cekCalendar) {
                    return $this->generalResponse(200, 'Set Default Data Calendar Success');
                } else {
                    return $this->failedResponse('Set Default Data Calendar Failed ');
                }
            } else {
                return $this->failedResponse('Set Default Data Calendar Failed ');
            }
        } else {
            return $this->responseNotFound('Data Calendar Not Found');
        }
    }

    public function destroy(Request $request, $id)
    {
        if (Str::length($id) > 20) {
            $id = ['The id must not be greater than 20 characters.'];

            return $this->invalidValidation($id);
        }
        try {
            $request->validate(
                ['deleted_by' => ['required', 'string', 'max:50']]
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

            $clientId = $request->deleted_by;
        $data = CoreCalendar::searchData($id)->first();

        if (null != $data) {
            $data->CSC_CAL_DEFAULT = 1;
            $data->CSC_CAL_DELETED_BY = $clientId;
            $data->CSC_CAL_DELETED_DT = Carbon::now('Asia/Jakarta');
            $data->save();

            if ($data) {
                return $this->generalResponse(200, 'Delete Data Calendar Success');
            } else {
                return $this->failedResponse('Delete Data Calendar Failed');
            }
        } else {
            return $this->responseNotFound('Data Calendar Not Found');
        }
    }

    public function filter(Request $request)
    {
        $items = (null != $request->items) ? $request->items : 10;

        $data = CoreCalendar::getData()
        ->where(
            function ($query) use ($request) {
                if (null != $request->name) {
                    $query->where('CSC_CAL_NAME', 'LIKE', '%'. $request->name.'%');
                }
            }
        )

        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        if (null != count($data)) {
            return response(
                new DataResponseResource(200, 'Filter Data Calendar Success', $data),
                200,
                ['Accept' => 'application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Filter Data Calendar Not Found'),
            404,
            ['Accept' => 'application/json']
        );
    }

    public function trash(Request $request)
    {
        $items = (null != $request->items) ? $request->items : 10;

        $data = CoreCalendar::getTrashData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_CAL_NAME', 'LIKE', '%'. $request->name.'%');
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
                new DataResponseResource(200, 'Get Trash Data Calendar Success', $data),
                200,
                ['Accept' => 'application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Trash Calendar Not Found'),
            404,
            ['Accept' => 'application/json']
        );
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
            $data = CoreCalendar::where('CSC_CAL_ID', $id)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Calendar Not Found'
                    ),
                    404
                );
            }

            CoreCalendar::where('CSC_CAL_ID', $id)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete Calendar Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete Calendar Failed',
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
            $checkAccount = $this->calendarSearchDeletedData($id[$i]);

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
            return $this->calendarNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->calendarSearchDeletedData($id[$n]);
                $data->CSC_CAL_DELETED_BY = null;
                $data->CSC_CAL_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Calendar Funds Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Calendar Funds Success But Some Data Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Calendar Funds Success');
    }
}
