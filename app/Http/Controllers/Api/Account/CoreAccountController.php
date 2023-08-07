<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\CoreAccount;
use App\Models\CoreBank;
use App\Traits\AccountTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoreAccountController extends Controller
{
    use ResponseHandler;
    use AccountTraits;


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
            'CSC_ACCOUNT_NUMBER AS NUMBER',
            'CSC_ACCOUNT_NAME AS NAME',
            'CSC_ACCOUNT_OWNER AS OWNER',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_ACCOUNT_NUMBER AS NUMBER',
            'BANK.CSC_BANK_CODE AS BANK_CODE',
            'BANK.CSC_BANK_NAME AS BANK',
            'CSC_ACCOUNT_NAME AS NAME',
            'CSC_ACCOUNT_OWNER AS OWNER',
            'CSC_ACCOUNT_TYPE AS TYPE',
            'CSC_ACCOUNT_CREATED_DT AS CREATED',
            'CSC_ACCOUNT_MODIFIED_DT AS MODIFIED',
            'CSC_ACCOUNT_CREATED_BY AS CREATED_BY',
            'CSC_ACCOUNT_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        // Logic Simple Config
        if ('simple' == $config) {
            // Logic Get Data
            $data = CoreAccount::getData()->get(self::getField());

            // Response sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Account Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Account Failed'
                );
            }

        // Logic Detail Config
        } elseif ('detail' == $config) {
            // Inisialisasi Variable
            $items = (null != $request->items) ? $request->items : 10;

            // Logic Get Data
            $data = CoreAccount::join(
                'CSCCORE_BANK AS BANK',
                'BANK.CSC_BANK_CODE',
                '=',
                'CSC_ACCOUNT_BANK'
            )
            ->getData()
            ->paginate(
                $perpage = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (null != count($data)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Account Success',
                    $config,
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

        // Logic Undefined Config
        } else {
            return $this->generalResponse(
                404,
                'Data Account Not Found'
            );
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi data mandatori
            $request->validate(
                [
                    'created_by' => ['required', 'string', 'max:50'],
                    'number' => 'required|numeric|digits_between:1,20',
                    'bank' => ['required', 'integer', 'digits_between:1,3'],
                    'name' => ['required', 'string', 'max:50'],
                    'owner' => ['required', 'string', 'max:50'],
                    'type' => ['required', 'integer', 'digits:1'],
                ]
            );

            // Inisialisasi Variable
            $number = $request->number;
            $bank = $request->bank;
            $created_by = $request->created_by;
            $name = $request->name;
            $owner = $request->owner;
            $type = $request->type;
            $clientId = $created_by;

            // Cek Bank
            $cekBank = CoreBank::searchData($bank)->first('CSC_BANK_NAME AS BANK_NAME');
            if (false == $cekBank) {
                return $this->generalResponse(
                    404,
                    'Data Bank Not Found'
                );
            }

            // Cek Status Deleted
            $statusDeleted = CoreAccount::searchTrashData($number)->first(self::getField());
            if (null != $statusDeleted) {
                return $this->generalResponse(
                    422,
                    'Unprocessable Entity'
                );
            }

            // Cek Data
            $data = CoreAccount::searchData($number)->first(self::getField());
            if (null != $data) {
                return $this->generalResponse(
                    409,
                    'Data Account Exists'
                );
            }

            // Logic Store Data
            $store = CoreAccount::create([
                'CSC_ACCOUNT_NUMBER' => $number,
                'CSC_ACCOUNT_BANK' => $bank,
                'CSC_ACCOUNT_NAME' => $name,
                'CSC_ACCOUNT_OWNER' => $owner,
                'CSC_ACCOUNT_TYPE' => $type,
                'CSC_ACCOUNT_CREATED_DT' => Carbon::now('Asia/Jakarta'),
                'CSC_ACCOUNT_CREATED_BY' => $clientId,
            ]);

            // Reesponse Sukses
            if ($store) {
                return $this->generalResponse(
                    200,
                    'Insert Data Account Success'
                );
            }

            // Response Failed
            if (!$store) {
                return $this->generalResponse(
                    500,
                    'Insert Data Account Failed'
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
            // Validasi Data Mandatory
            $request->validate(['number' => ['required', 'numeric', 'digits_between:1,20']]);

            // Logic Get Data
            $data = CoreAccount::join(
                'CSCCORE_BANK AS BANK',
                'BANK.CSC_BANK_CODE',
                '=',
                'CSC_ACCOUNT_BANK'
            )
            ->getData()
            ->searchData($request->number)
            ->first(self::getPaginate());

            // Response Sukses
            if (null != $data) {
                return $this->generalDataResponse(
                    200,
                    'Get Data Account Success',
                    $data
                );
            }

            // Response Not Found
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get Data Account Failed'
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
            // Validasi Data Mandatory
            $request->validate([
                'modified_by' => ['required', 'string', 'max:50'],
                'bank' => ['required', 'integer', 'digits_between:1,3'],
                'name' => ['required', 'string', 'max:50'],
                'owner' => ['required', 'string', 'max:50'],
                'type' => ['required', 'integer', 'digits:1'],
            ]);

            // Cek Account
            $data = CoreAccount::searchData($id)->first();
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    "Data Account Not Found"
                );
            }

            // Cek Bank
            $cekBank = CoreBank::searchData($request->bank)->first('CSC_BANK_NAME AS BANK_NAME');
            if (false == $cekBank) {
                return $this->generalResponse(
                    404,
                    'Data Bank Not Found'
                );
            }

            // Inisialisasi Variable
            $clientId = $request->modified_by;
            $bank = $request->bank;
            $name = $request->name;
            $owner = $request->owner;
            $type = $request->type;

            // Logic Update Data
            $data->CSC_ACCOUNT_BANK = $bank;
            $data->CSC_ACCOUNT_NAME = $name;
            $data->CSC_ACCOUNT_OWNER = $owner;
            $data->CSC_ACCOUNT_TYPE = $type;
            $data->CSC_ACCOUNT_MODIFIED_DT = Carbon::now('Asia/Jakarta');
            $data->CSC_ACCOUNT_MODIFIED_BY = $clientId;
            $data->save();

            // Response Sukses
            if ($data) {
                return $this->generalResponse(
                    200,
                    'Update Data Account Success'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Update Data Account Failed'
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
        try {
            // Validasi Data Mandatory
            if (Str::length($id) > 20) {
                $id = ['The id must not be greater than 20 characters.'];

                return $this->generalDataResponse(
                    400,
                    'Invalid Data Validation',
                    $id
                );
            }
            $request->validate(
                ['deleted_by' => ['required', 'string', 'max:50']]
            );

            // Cek Data
            $data = CoreAccount::searchData($id)->first();
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Logic Delete Data
            $clientId = $request->deleted_by;
            $data->CSC_ACCOUNT_DELETED_BY = $clientId;
            $data->CSC_ACCOUNT_DELETED_DT = Carbon::now('Asia/Jakarta');
            $data->save();

            // Response Sukses
            if ($data) {
                return $this->generalResponse(
                    200,
                    'Delete Data Account Success'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Update Data Account Failed'
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

    public function filter(Request $request)
    {
        // Inisialisasi Data Mandatori
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data
        $data = CoreAccount::join(
            'CSCCORE_BANK AS BANK',
            'BANK.CSC_BANK_CODE',
            '=',
            'CSC_ACCOUNT_BANK'
        )
        ->getData()
        ->getData()
        ->where(function ($query) use ($request) {
            if (null != $request->number) {
                $query->where('CSC_ACCOUNT_NUMBER', 'LIKE', '%'. $request->number.'%');
            }

            if (null != $request->bank) {
                $query->where('CSC_ACCOUNT_BANK', $request->bank);
            }

            if (null != $request->name) {
                $query->where('CSC_ACCOUNT_NAME', 'LIKE', '%'. $request->name.'%');
            }

            if (null != $request->owner) {
                $query->where('CSC_ACCOUNT_OWNER', 'LIKE', '%'. $request->owner.'%');
            }

            if (null != $request->type) {
                $query->where('CSC_ACCOUNT_TYPE', $request->type);
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
                'Filter Data Account Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Filter Data Account Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Filter Data Account Failed'
            );
        }
    }

    public function trash(Request $request)
    {
        // Inisialisasi Variable
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data
        $data = CoreAccount::join(
            'CSCCORE_BANK AS BANK',
            'BANK.CSC_BANK_CODE',
            '=',
            'CSC_ACCOUNT_BANK'
        )
        ->getTrashData()
        ->where(function ($query) use ($request) {
            if (null != $request->number) {
                $query->where('CSC_ACCOUNT_NUMBER', 'LIKE', '%'. $request->number.'%');
            }

            if (null != $request->bank) {
                $query->where('CSC_ACCOUNT_BANK', $request->bank);
            }

            if (null != $request->name) {
                $query->where('CSC_ACCOUNT_NAME', 'LIKE', '%'. $request->name.'%');
            }

            if (null != $request->owner) {
                $query->where('CSC_ACCOUNT_OWNER', 'LIKE', '%'. $request->owner.'%');
            }

            if (null != $request->type) {
                $query->where('CSC_ACCOUNT_TYPE', $request->type);
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
                'Get Trash Data Account Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Trash Account Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get Trasj Data Account Failed'
            );
        }
    }

    public function deleteData(Request $request, $id)
    {
        if (null == $id) {
            $id = ['id' => 'The id Field Is Required'];
            return $this->invalidValidation($id);
        }

        try {
            $data = CoreAccount::where('CSC_ACCOUNT_NUMBER', $id)->first();
            if (null == $data) {
                return response()->json(
                    new ResponseResource(
                        404,
                        'Data Account Not Found'
                    ),
                    404
                );
            }

            CoreAccount::where('CSC_ACCOUNT_NUMBER', $id)->delete();
            return response()->json(
                new ResponseResource(
                    200,
                    'Delete Account Success'
                ),
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                new ResponseResource(
                    500,
                    'Delete Account Failed',
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
                'number' => ['required', 'array'],
                'number.*' => ['string', 'max:20'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $number = $request->number;
        $count = count($number);
        $notFound = [];

        // Check Data
        for ($i=0; $i < $count; $i++) {
            $checkAccount = $this->accountSearchDeletedData($number[$i]);

            // Validasi Data
            if (false == $checkAccount) :
                $notFound = $number[$i];
                unset($number[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $number = array_values($number);
        $count = count($number);

        // Response  Not Found
        if (null == $count) :
            return $this->accountNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->accountSearchDeletedData($number[$n]);
                $data->CSC_ACCOUNT_DELETED_BY = null;
                $data->CSC_ACCOUNT_DELETED_DT = null;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Account Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(202, 'Restore Data Account  Success But Some Data Not Found', $response);
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Account Success');
    }
}
