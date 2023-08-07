<?php

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreCalendar;
use App\Models\CoreCalendarDay;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CoreCalendarDaysController extends Controller
{
    public function getSimpleField()
    {
        return [
            'CSC_CD_DATE AS DATE',
            'CSC_CD_DESC AS DESC',
        ];
    }

    public function cekCalendar($id)
    {
        return CoreCalendar::searchData($id)->first();
    }

    public function index(Request $request)
    {
        try {
            $request->validate(['id' => ['required', 'string', 'max:20']]);

            $cekCalendar = self::cekCalendar($request->id);
            if ($cekCalendar == null) {
                return response(
                    new ResponseResource(404, 'Data Calendar Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $data = CoreCalendarDay::join(
                'CSCCORE_CALENDAR AS CALENDAR',
                'CALENDAR.CSC_CAL_ID',
                '=',
                'CSC_CD_CALENDAR'
            )
            ->where(
                [
                    'CSC_CD_CALENDAR' => $request->id,
                    'CALENDAR.CSC_CAL_DELETED_DT' => null,
                ]
            )
            ->get(self::getSimpleField());

            if (count($data) == null) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Detail Calendar Not Found'
                    ),
                    404
                );
            } elseif (count($data) != null) {
                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get Data Detail Calendar Success',
                        $data
                    ),
                    200
                );
            } else {
                return response()->json(
                    new DataResponseResource(
                        500,
                        'Get Data Detail Calendar Failed',
                        $data
                    ),
                    500
                );
            }
        } catch (ValidationException $th) {
            return response()->json(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400
            );
        }
    }

    public function create()
    {
        //
    }

    public function destroy($id)
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
                            new ResponseResource(200, 'Delete Data Calendar-Biller Success'),
                            200,
                            ['Accept' => 'Application/json']
                        );
                    }

                    return response(
                        new DataResponseResource(500, 'Delete Data Calendar-Biller Failed', $data),
                        500,
                        ['Accept' => 'Application/json']
                    );
                }

                return response(
                    new ResponseResource(404, 'Data Calendar-Biller Not Found'),
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
