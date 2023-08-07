<?php

namespace App\Http\Controllers\Api\Biller;

use App\Http\Controllers\Controller;
use App\Models\CoreAccount;
use App\Models\CoreBiller;
use App\Models\CoreBillerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

use function PHPUnit\Framework\returnSelf;

class CoreBillerAccountController extends Controller
{
    public function listAccount(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $request->validate(['biller_id' => ['required', 'string', 'max:5']]);

            // Inisialisasi Variable
            $biller_id = $request->biller_id;
            $item = ($request->items != null) ? $request->items : 10;

            // Cek Data Biller
            $cekBiller = CoreBiller::searchData($request->biller_id)->first('CSC_BILLER_ID AS BILLER_ID');
            if (null == $cekBiller) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

            // Inisialisasi Field Response Json
            $filedListAccount = [
                'BA.CSC_BA_ID AS ID',
                'BANK.CSC_BANK_NAME AS BANK',
                'A.CSC_ACCOUNT_NUMBER AS ACCOUNT',
                'A.CSC_ACCOUNT_NAME AS ACCOUNT_NAME',
                'A.CSC_ACCOUNT_OWNER AS ACCOUNT_OWNER',
            ];

            // Logic Get Data
            $data = DB::connection('server_report')
            ->table('CSCCORE_BILLER_ACCOUNT AS BA')
            ->join(
                'CSCCORE_ACCOUNT AS A',
                'A.CSC_ACCOUNT_NUMBER',
                '=',
                'BA.CSC_BA_ACCOUNT'
            )
            ->join(
                'CSCCORE_BANK AS BANK',
                'A.CSC_ACCOUNT_BANK',
                '=',
                'BANK.CSC_BANK_CODE'
            )
            ->where('BA.CSC_BA_BILLER', $biller_id)
            ->paginate(
                $perpage = $item,
                $column = $filedListAccount,
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (null != count($data)) {
                return $this->generalDataResponse(
                    200,
                    'Get List Account-Biller Success',
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data Account-Biller Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Account-Biller Failed'
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

    public function dataAccount(Request $request)
    {
        try {
            // Validasi Data Mandatory
            $message = [
                'account.digits_between' => 'The account must not be greater than 20 characters.'
            ];

            $request->validate(['account' => ['required', 'numeric', 'digits_between:1,20']], $message);

            // Inisialisasi Variable
            $accountID = $request->account;

            // Cek Account
            $checkAccount = CoreAccount::where('CSC_ACCOUNT_NUMBER', $accountID)->first('CSC_ACCOUNT_NUMBER');
            if (null == $checkAccount) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Inisialisasi Response Field Json
            $filedListAccount = [
                'BANK.CSC_BANK_NAME AS BANK',
                'A.CSC_ACCOUNT_NUMBER AS ACCOUNT',
                'A.CSC_ACCOUNT_OWNER AS ACCOUNT_OWNER',
            ];

            // Logic Get Data
            $data = DB::connection('server_report')
            ->table('CSCCORE_ACCOUNT AS A')
            ->join(
                'CSCCORE_BANK AS BANK',
                'A.CSC_ACCOUNT_BANK',
                '=',
                'BANK.CSC_BANK_CODE'
            )
            ->where('A.CSC_ACCOUNT_NUMBER', $request->account)
            ->first($filedListAccount);

            // Response Sukses
            if (null != $data) {
                return $this->generalDataResponse(
                    200,
                    'Get Data Account-Biller Success',
                    $data
                );
            }

            // Response Not Found
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Data Account-Biller Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Data Account-Biller Failed'
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

    public function addBillerAccount(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $message = [
                'account.digits_between' => 'The account must not be greater than 20 characters.'
            ];

            $request->validate(
                [
                    'biller_id' => ['required', 'string', 'max:5'],
                    'account' => ['required', 'numeric', 'digits_between:1,20'],
                ],
                $message
            );

            // Inisialisasi Variable
            $biller_id = $request->biller_id;
            $account = $request->account;
            $keterangan = null;

            // Cek Biller
            $cekBiller = CoreBiller::searchData($biller_id)->first('CSC_BILLER_ID AS BILLER_ID');
            if (null == $cekBiller) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

            // Cek Account
            $cekAccount = CoreAccount::searchData($account)->first('CSC_ACCOUNT_NUMBER AS ACCOUNT');
            if (null == $cekAccount) {
                return $this->generalResponse(
                    404,
                    'Data Account Not Found'
                );
            }

            // Cek Biller Account Exists
            $cekBillerAccount = CoreBillerAccount::checkAccount($account, $biller_id)->first();
            if (null != $cekBillerAccount) {
                return $this->generalResponse(
                    409,
                    'Data Account-Biller Exists'
                );
            }

            // Cek Data Account Exists
            $cekAccountBiller = CoreBillerAccount::searchAccount($account)
            ->first();
            if (null != $cekAccountBiller) {
                return $this->generalResponse(
                    409,
                    'Data Account Exists'
                );
            }

            // Handle Duplicate UUID
            while ($keterangan == false) {
                $id = Uuid::uuid4();
                $cekId = CoreBillerAccount::searchData($id)->first();

                if (null == $cekId) {
                    $keterangan = true;
                } else {
                    $keterangan = false;
                }
            }

            // Logic Add Data
            $store = CoreBillerAccount::create(
                [
                    'CSC_BA_ID' => $id,
                    'CSC_BA_ACCOUNT' => $account,
                    'CSC_BA_BILLER' => $biller_id
                ]
            );

            // Response Sukses
            if ($store) {
                return $this->generalResponse(
                    200,
                    'Insert Data Account-Biller Success'
                );
            }

            // Response Failed
            if (!$store) {
                return $this->generalResponse(
                    500,
                    'Insert Data Account-Biller Failed'
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

    public function deleteAccount($id)
    {
        // Validasi Id
        if (null == $id) {
            return $this->generalResponse(
                400,
                'Invalid Data Validation'
            );
        }


        try {
            // Validasi Data Mandatori
            if (Str::length($id) > 36) {
                $id = ['The id must not be greater than 36 characters.'];

                return $this->generalDataResponse(
                    400,
                    'Invalid Data Validation',
                    $id
                );
            }

            // Cek Data Biller Account
            $data = CoreBillerAccount::searchData($id)->first();
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Data Account-Biller Not Found'
                );
            }

            // Logic Delete Data
            $destroy = $data->delete();

            // Response Sukses
            if ($destroy) {
                return $this->generalResponse(
                    200,
                    'Delete Data Account-Biller Success'
                );
            }

            // Response Failed
            if (!$destroy) {
                return $this->generalResponse(
                    500,
                    'Update Data Biller Failed'
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

    public function listAddAccount(Request $request)
    {
        // Inisialisasi Variable
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Data
        $data = CoreAccount::getData()
        ->whereNotExists(
            function ($query) {
                $query->select('BA.CSC_BA_ACCOUNT')
                ->from('CSCCORE_BILLER_ACCOUNT AS BA')
                ->whereColumn('BA.CSC_BA_ACCOUNT', 'CSC_ACCOUNT_NUMBER');
            }
        )
        ->get('CSC_ACCOUNT_NUMBER AS ACCOUNT');

        // Response Sukses
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Get List Add Account-Biller Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Get List Add Account-Biller Not Found'
            );
        endif;

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get List Add Account-Biller Failed'
            );
        }
    }
}
