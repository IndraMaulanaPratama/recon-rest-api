<?php

namespace App\Http\Controllers\Api\Biller;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreBillerCalendar;
use App\Models\CoreCalendar;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CoreBillerCalendarController extends Controller
{
    use ResponseHandler;

    public function cekBiller($id)
    {
        return CoreBiller::searchData($id)->first('CSC_BILLER_ID AS BILLER_ID');
    }

    public function cekCalendar($id)
    {
        return CoreCalendar::searchData($id)->first();
    }

    public function getPaginate()
    {
        return [
            'CSC_BC_ID AS ID',
            'CALENDAR.CSC_CAL_ID AS CAL_ID',
            'CALENDAR.CSC_CAL_NAME AS CAL_NAME'
        ];
    }

    public function getDataCalendar()
    {
        return [
            'CSC_CAL_ID AS ID',
            'CSC_CAL_NAME AS NAME'
        ];
    }


    public function index(Request $request)
    {
        try {
            $request->validate(['biller_id' => ['required', 'string', 'max:5']]);

            $cekBiller = self::cekBiller($request->biller_id);
            if (null == $cekBiller) {
                return response(
                    new ResponseResource(404, 'Data Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $items = ($request->items == null) ? 10 : $request->items;
            $data = CoreBillerCalendar::join(
                'CSCCORE_CALENDAR AS CALENDAR',
                'CALENDAR.CSC_CAL_ID',
                '=',
                'CSC_BC_CALENDAR'
            )
            ->join(
                'CSCCORE_BILLER AS BILLER',
                'BILLER.CSC_BILLER_ID',
                '=',
                'CSC_BC_BILLER'
            )
            ->where('BILLER.CSC_BILLER_ID', $request->biller_id)
            ->paginate(
                $items = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            if (null != count($data)) {
                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get List Calendar-Biller Success',
                        $data
                    ),
                    200
                );
            } elseif (null == count($data)) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Calendar-Biller Not Found'
                    ),
                    404
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Get List Data Calendar-Biller Failed'
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'biller_id' => ['required', 'string', 'max:5'],
                'calendar' => ['required', 'string', 'max:20']
            ]);

            $cekBiller = self::cekBiller($request->biller_id);
            if (null == $cekBiller) {
                return response(
                    new ResponseResource(404, 'Data Biller Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $cekCalendar = self::cekCalendar($request->calendar);
            if ($cekCalendar == null) {
                return response(
                    new ResponseResource(404, 'Data Calendar Not Found'),
                    404,
                    ['Accept' => 'Application/json']
                );
            }

            $cekDeletedStatus = CoreBillerCalendar::searchByBillerAndCalendar(
                $request->calendar,
                $request->biller_id
            )
            ->first();
            if ($cekDeletedStatus != null) {
                return response(
                    new ResponseResource(409, 'Data Calendar-Biller Exists'),
                    409,
                    ['Accept' => 'Application/json']
                );
            }

            $keterangan = null;
            while ($keterangan == false) {
                $id = Uuid::uuid4();
                $cekId = CoreBillerCalendar::searchData($id)->first();

                if (null == $cekId) {
                    $keterangan = true;
                } else {
                    $keterangan = false;
                }
            }

            $data = CoreBillerCalendar::create(
                [
                    'CSC_BC_ID' => $id,
                    'CSC_BC_CALENDAR' => $request->calendar,
                    'CSC_BC_BILLER' => $request->biller_id
                ]
            );

            if ($data) {
                return response()->json(
                    new ResponseResource(
                        200,
                        'Insert Data Calendar-Biller Success'
                    ),
                    200
                );
            } else {
                return response()->json(
                    new ResponseResource(
                        500,
                        'Insert Data Calendar-Biller Failed'
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

    public function show(Request $request)
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

            $data = CoreCalendar::getData()
            ->where('CSC_CAL_ID', $request->id)
            ->first(self::getDataCalendar());

            if ($data == null) {
                return response()->json(
                    new ResponseResource(//code...
                        400,
                        'Data Calendar Not Found'
                    ),
                    200
                );
            } elseif ($data != null) {
                return response()->json(
                    new DataResponseResource(
                        200,
                        'Get Data Calendar-Biller Success',
                        $data
                    ),
                    200
                );
            } else {
                return response()->json(
                    new DataResponseResource(
                        500,
                        'Get Data Calendar-Biller Failed',
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

    public function destroy(Request $request, $id)
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

                $dataCalendarBiller = CoreBillerCalendar::searchData($id)->first();
                if (null == $dataCalendarBiller) {
                    return response()->json(
                        new ResponseResource(404, 'Data Calendar-Biller Not Found'),
                        404
                    );
                }

                $deleteData = CoreBillerCalendar::searchData($id)->delete();
                if ($deleteData) {
                    return response()->json(
                        new ResponseResource(200, 'Delete Data Calendar-Biller Success'),
                        200
                    );
                } else {
                    return response()->json(
                        new ResponseResource(500, 'Delete Data Calendar-Biller Failed'),
                        500
                    );
                }
            } catch (ValidationException $th) {
                return response(new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()));
            }
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }
}
