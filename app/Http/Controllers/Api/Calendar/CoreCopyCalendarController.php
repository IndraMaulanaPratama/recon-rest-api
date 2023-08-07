<?php

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataErrorResource;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreCalendar;
use App\Models\CoreCalendarDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class CoreCopyCalendarController extends Controller
{
    public function getClientId($bearer)
    {
        $bearer_value = substr($bearer, 7);
        $clientId = DB::connection('recon_auth')
        ->table('CSCMOD_CLIENT')
        ->where('csm_c_bearer', $bearer_value)
        ->pluck('csm_c_id');

        return $clientId[0];
    }

    public function getDataCopy(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:20']]);

            $cekCalendar = CoreCalendar::searchData($request->id)->first('CSC_CAL_ID');
            if (null == $cekCalendar) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Calendar Not Found'
                    ),
                    404
                );
            }

            $items = ($request->items == null) ? 10 : $request->items;
            $data = CoreCalendar::getData()
            ->join(
                'CSCCORE_CALENDAR_DAYS AS CD',
                'CD.CSC_CD_CALENDAR',
                '=',
                'CSC_CAL_ID'
            )
            ->where('CSC_CAL_ID', $request->id)
            ->first(
                [
                    'CSC_CAL_NAME AS NAME',
                    'CSC_CAL_DEFAULT AS DEFAULT',
                ]
            );

            $dataDays = CoreCalendar::getData()
            ->join(
                'CSCCORE_CALENDAR_DAYS AS CD',
                'CD.CSC_CD_CALENDAR',
                '=',
                'CSC_CAL_ID'
            )
            ->where('CSC_CAL_ID', $request->id)
            ->get(
                [
                    'CSC_CD_DATE AS date',
                    'CSC_CD_DESC AS desc',
                ]
            );

            if (null != $data) {
                $data = collect($data);
                $data->put('days', $dataDays);

                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get Data Copy Calendar Success',
                        $data
                    ),
                    200
                );
            } elseif (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Copy Calendar Not Found'
                    ),
                    404
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Get Data Copy Calendar Failed'
                    ),
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

    public function calendarCopy(Request $request)
    {
        try {
            $request->validate(
                [
                    'created_by' => ['required', 'string', 'max:50'],
                    'id_cal' => ['required', 'string', 'max:20'],
                    'name' => ['required', 'string', 'max:100'],
                    'days' => ['array'],
                    'days.*.date' => ['date_format:Y-m-d'],
                    'days.*.desc' => ['string', 'max:50'],
                ]
            );

            $clientId = $request->created_by;
            $cekCalendar = CoreCalendar::searchData($request->id_cal)->first('CSC_CAL_ID');
            if (null != $cekCalendar) {
                return response()->json(
                    new ResponseResource(
                        409,
                        'Data Calendar Exists'
                    ),
                    409
                );
            }

            $cekDeleted = CoreCalendar::searchTrashData($request->id_cal)->first();
            if (null != $cekDeleted) {
                return response()->json(
                    new ResponseResource(
                        422,
                        'Unprocessable Entity'
                    ),
                    422
                );
            }

            $storeCalendar = CoreCalendar::create(
                [
                    'CSC_CAL_ID' => $request->id_cal,
                    'CSC_CAL_NAME' => $request->name,
                    'CSC_CAL_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                    'CSC_CAL_CREATED_BY' => $clientId,
                ]
            );

            if (!$storeCalendar) {
                return response()->json(
                    new DataResponseResource(
                        500,
                        'Copy Data Calendar Failed',
                        $storeCalendar
                    ),
                    500
                );
            }

            $storeDays = [];
            $warningStore = [];
            $countDays = count($request->days);
            for ($i=0; $i < $countDays; $i++) {
                $calendar = $request->id_cal;
                $date = $request->days[$i]['date'];
                $desc = $request->days[$i]['desc'];

                $storeDays[$i] = CoreCalendarDay::create(
                    [
                        'CSC_CD_ID' => Uuid::uuid4(),
                        'CSC_CD_CALENDAR' => $calendar,
                        'CSC_CD_DATE' => $date,
                        'CSC_CD_DESC' => $desc,
                    ]
                );

                if (!$storeDays[$i]) {
                    $warningStore[] = $storeDays[$i];
                }
            }

            if (null == $warningStore) {
                return response()->json(
                    new ResponseResource(
                        200,
                        'Copy Data Calendar Success'
                    ),
                    200
                );
            } else {
                return response()->json(
                    new DataErrorResource(
                        500,
                        'Copy Data Calendar Failed',
                        $warningStore
                    ),
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

    public function listDay(Request $request)
    {
        try {
            $request->validate(
                [
                    'calendar' => ['required', 'string', 'max:20'],
                ]
            );

            $cekCalendar = CoreCalendar::searchData($request->calendar)->first('CSC_CAL_ID');
            if (null == $cekCalendar) {
                return $this->responseNotFound('Data Calendar Not Found');
            }

            $items = ($request->items != null) ? $request->items : 10;
            $data = CoreCalendar::getData()
            ->join(
                'CSCCORE_CALENDAR_DAYS AS CD',
                'CD.CSC_CD_CALENDAR',
                '=',
                'CSC_CAL_ID'
            )
            ->where('CSC_CAL_ID', $request->calendar)
            ->paginate(
                $items = $items,
                $column = [
                    'CD.CSC_CD_ID AS ID',
                    'CD.CSC_CD_DATE AS DATE',
                    'CD.CSC_CD_DESC AS DESC',
                ]
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null != count($data)) {
                return $this->generalDataResponse(200, 'Get List Calendar Days Success', $data);
            } elseif (null == count($data)) {
                return $this->responseNotFound('Data Calendar Days Not Found');
            } else {
                return $this->failedResponse('Get List Calendar Days Failed');
            }

            return $data;
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }
    }

    public function addDay(Request $request)
    {
        try {
            $request->validate(
                [
                    'calendar' => ['required', 'string', 'max:20'],
                    'date' => ['required', "date_format:Y-m-d"],
                    'desc' => ['required', 'string', 'max:50'],
                ]
            );

            $cekCalendar = CoreCalendar::searchData($request->calendar)->first('CSC_CAL_ID AS id');
            if (null == $cekCalendar) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Calendar Not Found'
                    ),
                    404
                );
            } else {
                $cekCalendarDay = CoreCalendarDay::checkData($request->calendar, $request->date)
                ->first('CSC_CD_CALENDAR AS id');
                if ($cekCalendarDay != null) {
                    return response()->json(
                        new ResponseResource(
                            409,
                            'Data Calendar Days Exists'
                        ),
                        409
                    );
                }
            }

            $keterangan = null;
            while ($keterangan == false) {
                $id = Uuid::uuid4();
                $cekId = CoreCalendarDay::searchData($id)->first();

                if (null == $cekId) {
                    $keterangan = true;
                } else {
                    $keterangan = false;
                }
            }

            $data = CoreCalendarDay::create(
                [
                    'CSC_CD_ID' => $id,
                    'CSC_CD_CALENDAR' => $request->calendar,
                    'CSC_CD_DATE' => $request->date,
                    'CSC_CD_DESC' => $request->desc,
                ]
            );

            if ($data) {
                return response()->json(
                    new ResponseResource(
                        200,
                        'Insert Data Calendar Days Success'
                    ),
                    200
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Insert Data Calendar Days Failed'
                    ),
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

    public function getDataDay(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:36']]);

            $data = CoreCalendarDay::searchData($request->id)->first(
                [
                    'CSC_CD_ID AS ID',
                    'CSC_CD_DATE AS DATE',
                    'CSC_CD_DESC AS DESC',
                ]
            );
            if (null != $data) {
                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get Data Calendar Days Success',
                        $data
                    ),
                    200
                );
            } elseif (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Calendar Days Not Found'
                    ),
                    404
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Get Data Calendar Days Failed'
                    ),
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

    public function updateDay(Request $request, $id)
    {
        if (null == $id) {
            if (Str::length($id) > 36) {
                $id = ['The id must not be greater than 36 characters.'];

                return response(
                    new DataResponseResource(400, 'Invalid Data Validation', $id),
                    400,
                    ['Accept' => 'application/json']
                );
            }
        }

        try {
            $request->validate(
                [
                    'date' => ['required', 'date_format:Y-m-d'],
                    'desc' => ['required', 'string', 'max:50'],
                ]
            );

            $cekCalendarDay = CoreCalendarDay::searchData($id)->first();
            if ($cekCalendarDay == null) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Calendar Days Not Found'
                    ),
                    404
                );
            }

            $update = $cekCalendarDay;
            $update->CSC_CD_DATE = $request->date;
            $update->CSC_CD_DESC = $request->desc;
            $update->save();

            if ($update) {
                return response()->json(
                    new ResponseResource(
                        200,
                        'Update Data Calendar Days Success'
                    ),
                    200
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Update Data Calendar Days Failed'
                    ),
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

    public function deleteDay(Request $request, $id)
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

                $data = CoreCalendarDay::searchData($id)->first();

                if (null != $data) {
                    $data->delete();

                    if ($data) {
                        return response(
                            new ResponseResource(200, 'Delete Data Calendar Days Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new ResponseResource(500, 'Delete Data Calendar Days Failed'),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Calendar Days Not Found'),
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
}
