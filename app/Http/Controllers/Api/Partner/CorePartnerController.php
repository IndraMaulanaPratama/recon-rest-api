<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Models\CorePartner;
use App\Traits\PartnerTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CorePartnerController extends Controller
{
    use ResponseHandler;
    use PartnerTraits;

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
            'CSC_PARTNER_ID AS ID',
            'CSC_PARTNER_NAME AS NAME',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_PARTNER_ID AS ID',
            'CSC_PARTNER_NAME AS NAME',
            'CSC_PARTNER_CREATED_DT AS CREATED',
            'CSC_PARTNER_MODIFIED_DT AS MODIFIED',
            'CSC_PARTNER_CREATED_BY AS CREATED_BY',
            'CSC_PARTNER_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        // Logic Simple Config
        if ('simple' == $config) {
            // Logic Get Data Partner
            $data = CorePartner::getData()->get(self::getField());

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Partner Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    400,
                    'Data Partner Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Partner Failed',
                );
            }

        // Logic Detail Config
        } elseif ('detail' == $config) {
            // Inisialisasi Variable
            $items = (null != $request->items) ? $request->items : 10;

            // Logic Get Data Partner
            $data = CorePartner::getData()->paginate(
                $perpage = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Partner Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Partner Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Partner Failed',
                );
            }

        // Logic Invalid Config
        } else {
            return $this->generalResponse(
                404,
                'Data Partner Not Found'
            );
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi data Mandatory
            $request->validate(
                [
                    'created_by' => ['required', 'string', 'max:50'],
                    'id' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:100'],
                ]
            );

            // Inisialisasi Variable
            $clientId = $request->created_by;
            $id = $request->id;
            $name = $request->name;

            // Cek Status Deleted
            $statusDeleted = CorePartner::searchTrashData($id)->first(self::getField());
            if (null != $statusDeleted) {
                return $this->generalResponse(
                    422,
                    'Unprocessable Entity'
                );
            }

            // Cek Data Exist
            $cekData = CorePartner::searchData($id)->first(self::getField());
            if (null != $cekData) {
                return $this->generalResponse(
                    409,
                    'Data Partner Exists'
                );
            }

            // Logic Add Partner
            $store = CorePartner::create([
                'CSC_PARTNER_ID' => $id,
                'CSC_PARTNER_NAME' => $name,
                'CSC_PARTNER_CREATED_BY' => $clientId,
                'CSC_PARTNER_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            ]);

            // Response Sukses
            if ($store) {
                return $this->generalResponse(
                    200,
                    'Insert Data Partner Success'
                );
            }

            // Response Failed
            if (!$store) {
                return $this->generalResponse(
                    500,
                    'Isert Data Partner Failed '
                );
            }
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }
    }

    public function show(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(['id' => ['required', 'string', 'max:50']]);

            // Logic Get Data
            $partner = CorePartner::searchData($request->id)->first(self::getField());
            if (null == $partner) {
                return $this->generalResponse(
                    404,
                    'Data Partner Not Found'
                );
            }

            // Response Sukses
            if (null != $partner) {
                return $this->generalDataResponse(
                    200,
                    'Get Data Partner Success',
                    $partner
                );
            }
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(
                [
                    'modified_by' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:100']
                ]
            );

            // Inisialisasi Variable
            $name = $request->name;
            $clientId = $request->modified_by;

            // Cek Data Partner
            $cekPartner = CorePartner::searchData($id)->first();
            if (null == $cekPartner) {
                return $this->generalResponse(
                    404,
                    'Data Partner Not Found'
                );
            }

            // Logic Update Data
            $cekPartner->CSC_PARTNER_NAME = $name;
            $cekPartner->CSC_PARTNER_MODIFIED_DT = Carbon::now('Asia/Jakarta');
            $cekPartner->CSC_PARTNER_MODIFIED_BY = $clientId;
            $cekPartner->save();

            // Response Sukses
            if ($cekPartner) {
                return $this->generalResponse(
                    200,
                    'Update Data Partner Success'
                );
            }

            // Response Failed
            if (!$cekPartner) {
                return $this->generalResponse(
                    500,
                    'Update Data Partner Failed'
                );
            }
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        // Validasi Data Mandatory
        if (null != $id) {
            try {
                if (Str::length($id) > 50) {
                    $id = ['The id must not be greater than 50 characters.'];

                    return $this->generalDataResponse(
                        400,
                        'Invalid Data Validation',
                        $id
                    );
                }

                // Validasi Data Mandatory
                $request->validate(
                    ['deleted_by' => ['required', 'string', 'max:50']]
                );

                // Inisialisasi Variable
                $clientId = $request->deleted_by;

                // Logic Get Data
                $data = CorePartner::searchData($id)->first();

                // Cek Data
                if (null == $data) {
                    return $this->generalResponse(
                        404,
                        'Data Partner Not Found'
                    );
                }

                // Logic Delete Data
                $data->CSC_PARTNER_DELETED_BY = $clientId;
                $data->CSC_PARTNER_DELETED_DT = Carbon::now('Asia/Jakarta');
                $data->save();

                // Response Sukses
                if ($data) {
                    return $this->generalResponse(
                        200,
                        'Delete Data Partner Success'
                    );
                }

                // Response Failed
                if (!$data) {
                    return $this->generalResponse(
                        500,
                        'Update Data Partner Failed'
                    );
                }
            } catch (ValidationException $th) {
                return $this->generalDataResponse(
                    400,
                    'Invalid Data Validation',
                    $th->validator->errors()
                );
            }
        } else {
            return $this->generalResponse(
                400,
                'Invalid Data Validation'
            );
        }
    }

    public function filter(Request $request)
    {
        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data
        $partner = CorePartner::getData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_PARTNER_NAME', 'LIKE', '%'. $request->name.'%');
            }
        })
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $partner = $this->addIndexNumber($partner);

        // Response Sukses
        if (null != count($partner)) {
            return $this->generalDataResponse(
                200,
                'Filter Data Partner Success',
                $partner
            );
        }

        // Response Not Found
        if (null == count($partner)) {
            return $this->generalResponse(
                404,
                'Filter Data Partner Not Found'
            );
        }

        // Response Failed
        if (!$partner) {
            return $this->generalResponse(
                500,
                'Filter Data Partner Filed'
            );
        }
    }

    public function trash(Request $request)
    {
        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;

        // Logit Get Data
        $partner = CorePartner::getTrashData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_PARTNER_NAME', 'LIKE', '%'. $request->name.'%');
            }
        })
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $partner = $this->addIndexNumber($partner);

        // Response Sukses
        if (null != count($partner)) {
            return $this->generalDataResponse(
                200,
                'Get Trash Data Partner Success',
                $partner
            );
        }

        // Response Not Found
        if (null == count($partner)) {
            return $this->generalResponse(
                404,
                'Data Trash Partner Not Found'
            );
        }
    }

    public function deleteData(Request $request, $id)
    {
        // Cek Request Id
        if (null == $id) {
            $id = ['id' => 'The id Field Is Required'];

            // Response Invalid Data Mandatory
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $id
            );
        }

        // Logic Get Data
        $data = CorePartner::where('CSC_PARTNER_ID', $id)->first();

        // Response Not Found
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Partner Not Found'
            );
        }

        // Logic Delete data
        CorePartner::where('CSC_PARTNER_ID', $id)->delete();

        // Response Sukses
        if (null != $data) {
            return $this->generalResponse(
                200,
                'Delete Partner Success'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Delete Partner Failed',
            );
        }
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['string', 'max:50']
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $count = count($id);
        $notFound = [];

        // Check Deleted Data Partner
        for ($i=0; $i < $count; $i++) {
            $checkData = $this->partnerGetDeletedData($id[$i]);

            // Validasi Data Partner
            if (false == $checkData) :
                $notFound[] = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response Data Partner Not Found
        if (null == $count) :
            return $this->partnerNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) {
                $data = $this->partnerGetDeletedData($id[$n]);
                $data->CSC_PARTNER_DELETED_BY = null;
                $data->CSC_PARTNER_DELETED_DT = null;
                $data->save();
            }
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Partner Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(202, 'Restore Data Partner Success But Some Data Not Found', $response);
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Partner Success');
    }
}
