<?php

namespace App\Http\Controllers\Api\Biller;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreGroupOfProduct;
use App\Models\CoreProfileFee;
use App\Traits\BillerTraits;
use App\Traits\ProfileTraits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoreBillerController extends Controller
{
    use ResponseHandler;
    use BillerTraits;
    use ProfileTraits;

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
            'CSC_BILLER_ID AS ID',
            'CSC_BILLER_GROUP_PRODUCT AS GROUP_OF_PRODUCT',
            'CSC_BILLER_NAME AS NAME',
            'CSC_BILLER_PROFILE AS PROFILE',
            'PF.CSC_PROFILE_NAME AS PROFILE_NAME',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_BILLER_ID AS ID',
            'CSC_BILLER_GROUP_PRODUCT AS GROUP_OF_PRODUCT',
            'CSC_BILLER_PROFILE AS PROFILE',
            'PF.CSC_PROFILE_NAME AS PROFILE_NAME',
            'CSC_BILLER_NAME AS NAME',
            'CSC_BILLER_CREATED_DT AS CREATED',
            'CSC_BILLER_MODIFIED_DT AS MODIFIED',
            'CSC_BILLER_CREATED_BY AS CREATED_BY',
            'CSC_BILLER_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    public function index(Request $request, $config)
    {
        // Logic Config List Simple
        if ('simple' == $config) {
            // Get Data Biller
            $biller = CoreBiller::getData()
            ->join(
                'CSCCORE_PROFILE_FEE AS PF',
                'CSC_BILLER_PROFILE',
                '=',
                'PF.CSC_PROFILE_ID'
            )
            ->get(self::getField());

            // Hitung Jumlah Data
            $count = count($biller);

            // Mapping Profile
            for ($i=0; $i < $count; $i++) {
                $profile = $biller[$i]['PROFILE'] .' - '. $biller[$i]['PROFILE_NAME'];

                $biller[$i] = collect($biller[$i]);
                $biller[$i]->put('PROFILE', $profile);
                $biller[$i]->forget('PROFILE_NAME');
            }

            // Response Sukses
            if (null != count($biller)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Biller Success',
                    $config,
                    $biller
                );
            }

            // Response Not Found
            if (null == count($biller)) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

            // Response Failed
            if (!$biller) {
                return $this->generalResponse(
                    500,
                    'Get List Biller Success',
                );
            }

        // Logic Config Detail
        } elseif ('detail' == $config) {
            // Inisialisasi Variable yang dibutuhkan
            $items = (null != $request->items) ? $request->items : 10;

            // Logic Get Data Biller
            $biller = CoreBiller::getData()
            ->join(
                'CSCCORE_PROFILE_FEE AS PF',
                'CSC_BILLER_PROFILE',
                '=',
                'PF.CSC_PROFILE_ID'
            )
            ->paginate($perpage = $items, $column = self::getPaginate());

            // Add Index Number
            $count = count($biller);

            // Mapping Profile
            for ($i=0; $i < $count; $i++) {
                $profile = $biller[$i]['PROFILE'] .' - '. $biller[$i]['PROFILE_NAME'];

                $biller[$i] = collect($biller[$i]);
                $biller[$i]->put('PROFILE', $profile);
                $biller[$i]->forget('PROFILE_NAME');
            }

            // Add Index Number
            $biller = $this->addIndexNumber($biller);

            // Response Sukses
            if (null != count($biller)) {
                return $this->generalConfigResponse(
                    200,
                    'Get List Biller Success',
                    $config,
                    $biller
                );
            }

            // Reponse Not Found
            if (null == count($biller)) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

            // Response Failed
            if (!$biller) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

        // Response Invalid Config
        } else {
            return $this->generalResponse(
                404,
                'Data Biller Not Found'
            );
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi Data Mandatori
            $request->validate([
                'created_by' => ['required', 'string', 'max:50'],
                'id' => ['required', 'string', 'max:5'],
                'gop' => ['required', 'string', 'max:50'],
                'profile' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:100'],
                'created' => ['string', 'max:19'],
                'modified' => ['string', 'max:19'],
                'modified_by' => ['string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $clientId = $request->created_by;
        $gop = $request->gop;
        $profile = $request->profile;
        $id = $request->id;

        // Cek GOP
        $cekGOP = CoreGroupOfProduct::searchBiller($gop)->first('CSC_GOP_PRODUCT_NAME');
        if (false == $cekGOP) {
            return $this->generalResponse(
                404,
                'Data Group Of Product Not Found'
            );
        }

        // Cek Exists GOP
        $cekGopExists = CoreBiller::where('CSC_BILLER_GROUP_PRODUCT', $gop)
        ->first('CSC_BILLER_GROUP_PRODUCT AS BILLER_GROUP_PRODUCT');
        if ($cekGopExists != null) {
            return $this->generalResponse(
                409,
                'Data Group Of Product Exists'
            );
        }

        // Cek Data Profile
        $checkProfile = $this->profileById($profile);

        // Response Profile Not Found
        if (false == $checkProfile) :
            return $this->profileNotFound();
        endif;

        // Cek Status Deleted
        $checkDeleted = $this->billerGetTrashData($id);

        // Response Deleted Status
        if (false != $checkDeleted) :
            return $this->responseUnprocessable();
        endif;

        // Cek Data Biller Exists
        $cekBiller = $this->billerById($id);

        // Response Biller Exists
        if (false != $cekBiller) {
            return $this->generalResponse(
                409,
                'Data Biller Exists'
            );
        }

        // Logic Add Data
        $store = CoreBiller::create([
            'CSC_BILLER_ID' => $request->id,
            'CSC_BILLER_GROUP_PRODUCT' => $request->gop,
            'CSC_BILLER_PROFILE' => $profile,
            'CSC_BILLER_NAME' => $request->name,
            'CSC_BILLER_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            'CSC_BILLER_MODIFIED_DT' => null,
            'CSC_BILLER_CREATED_BY' => $clientId,
            'CSC_BILLER_MODIFIED_BY' => null,
        ]);


        // Response Sukses
        if ($store) {
            return $this->generalResponse(
                200,
                'Insert Data Biller Success'
            );
        }

        // Response Failed
        if (!$store) {
            return $this->generalResponse(
                500,
                'Insert Data Biller Failed'
            );
        }
    }

    public function show(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(['id' => ['required', 'string', 'max:5']]);
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $id = $request->id;

        // Logic Get Data
        try {
            $data = $this->billerGetData($id);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Biller Failed', $th->getMessage());
        }

        // Response Not Found
        if (false == $data) {
            return $this->responseNotFound('Data Biller Not Found');
        }

        // Response Sukses
        return $this->generalDataResponse(200, 'Get Data Biller Success', $data);
    }

    public function update(Request $request, $id)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'modified_by' => ['required', 'string', 'max:50'],
                    'name' => ['required', 'string', 'max:100'],
                    'profile' => ['required', 'string', 'max:50'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable yang dibutuhkan
        $clientId = $request->modified_by;
        $name = $request->name;
        $profile = $request->profile;

        // Cek Data Biller
        $biller = $this->billerById($id);

        // Response Biller Not Found
        if (false == $biller) :
            return $this->billerNotFound();
        endif;

        // Cek Data Profile
        $checkProfile = $this->profileById($profile);

        // Response Profile Not Found
        if (false == $checkProfile) :
            return $this->profileNotFound();
        endif;

        // Logic Update Data
        try {
            $biller = $this->billerById($id);
            $biller->CSC_BILLER_NAME = $name;
            $biller->CSC_BILLER_PROFILE = $profile;
            $biller->CSC_BILLER_MODIFIED_DT = Carbon::now('Asia/Jakarta');
            $biller->CSC_BILLER_MODIFIED_BY = $clientId;
            $biller->save();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Update Data Biller Failed', $th->getMessage());
        }

        // Response Sukses
        if ($biller) {
            return $this->generalResponse(
                200,
                'Update Data Biller Success'
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        if (null != $id) {
            try {
                // Handle ID Mandatori
                if (Str::length($id) > 5) {
                    $id = ['The id must not be greater than 5 characters.'];

                    // Response Invalid Data Mandatory
                    return $this->generalDataResponse(
                        400,
                        'Invalid Data Validation',
                        $id
                    );
                }

                // Validasi Data <andatory
                $request->validate(
                    ['deleted_by' => ['required', 'string', 'max:50']]
                );

                // Inisialisasi Variable yang dibutuhkan
                $clientId = $request->deleted_by;

                // Cek Data Biller
                $data = CoreBiller::searchData($id)->first();
                if (null == $data) {
                    return $this->generalResponse(
                        404,
                        'Data Biller Not Found'
                    );
                }

                // Logic Delete Data
                $data->CSC_BILLER_DELETED_BY = $clientId;
                $data->CSC_BILLER_DELETED_DT = Carbon::now('Asia/Jakarta');
                $data->save();

                // Response Sukses
                if ($data) {
                    return $this->generalResponse(
                        200,
                        'Delete Data Biller Success'
                    );
                }

                // Response Failed
                if (!$data) {
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
        } else {
            return response(new ResponseResource(400, 'Invalid Data Validation'));
        }
    }

    public function filter(Request $request)
    {
        // Inisialisasi Variable yang dibutuhkan
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Filter Data
        $filter = CoreBiller::getData()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_BILLER_NAME', 'LIKE', '%'. $request->name.'%');
            }

            if (null != $request->gop) {
                $query->where('CSC_BILLER_GROUP_PRODUCT', $request->gop);
            }
        })
        ->joinProfileFee()
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate(),
        );

        // Hitung Jumlah Data
        $count = count($filter);

        // Mapping Profile
        for ($i=0; $i < $count; $i++) {
            $profile = $filter[$i]['PROFILE'] .' - '. $filter[$i]['PROFILE_NAME'];

            $filter[$i] = collect($filter[$i]);
            $filter[$i]->put('PROFILE', $profile);
            $filter[$i]->forget('PROFILE_NAME');
        }

        // Add Index Number
        $filter = $this->addIndexNumber($filter);

        // Response Success
        if (null != count($filter)) {
            return $this->generalDataResponse(
                200,
                'Filter Data Biller Success',
                $filter
            );
        }

        // Response Not Found
        if (null == count($filter)) {
            return $this->generalResponse(
                404,
                'Filter Data Biller Not Found'
            );
        }

        // Response Failed
        if (!$filter) {
            return $this->generalResponse(
                500,
                'Filter Data Biller Failed'
            );
        }
    }

    public function trash(Request $request)
    {
        // Inisialisasi Variable yang dibutuhkan
        $items = (null != $request->items) ? $request->items : 10;

        // Logic Get Data Trash
        $filter = CoreBiller::getTrashData()
        ->joinProfileFee()
        ->where(function ($query) use ($request) {
            if (null != $request->name) {
                $query->where('CSC_BILLER_NAME', 'LIKE', '%'. $request->name.'%');
            }

            if (null != $request->gop) {
                $query->where('CSC_BILLER_GROUP_PRODUCT', 'LIKE', '%'. $request->gop.'%');
            }
        })
        ->paginate(
            $perpage = $items,
            $column = self::getPaginate(),
        );

        // Response Sukses
        if (null != count($filter)) {
            // Add Index Number
            $filter = $this->addIndexNumber($filter);

            return $this->generalDataResponse(
                200,
                'Get Data Trash Biller Success',
                $filter
            );
        }

        // Response Not Found
        if (null == count($filter)) {
            return $this->generalResponse(
                404,
                'Data Trash Biller Not Found'
            );
        }

        // Response Failed
        if (!$filter) {
            return $this->generalDataResponse(
                500,
                'Get Data Trash Biller Failed',
                $filter
            );
        }
    }

    public function listGop()
    {
        // Logic Get Data GOP
        $data = CoreGroupOfProduct::distinct()->get('CSC_GOP_PRODUCT_GROUP AS gop');

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Group Of Product Not Found'
            );
        }

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Get Data Group Of Product Success',
                $data
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get Data Group Of Product Failed'
            );
        }
    }

    public function deleteData(Request $request, $id)
    {
        // Validasi Id
        if (null == $id) {
            $id = ['id' => 'The id Field Is Required'];

            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $id
            );
        }

        try {
            // Cek Data Biller
            $data = CoreBiller::where('CSC_BILLER_ID', $id)->first();
            if (null == $data) {
                return $this->generalResponse(
                    404,
                    'Data Biller Not Found'
                );
            }

            // Logic Delete Data
            $destroy = CoreBiller::where('CSC_BILLER_ID', $id)->delete();

            // Response Sukses
            if ($destroy) {
                return $this->generalResponse(
                    200,
                    'Delete Biller Success'
                );
            }

            // Response Failed
            if (!$$destroy) {
                return $this->generalResponse(
                    500,
                    'Delete Biller Failed'
                );
            }
        } catch (\Throwable $th) {
            return $this->generalDataResponse(
                500,
                'Delete Biller Failed',
                $th
            );
        }
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['string', 'max:5']
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $count = count($id);
        $notFound = [];

        // Check Data Biller
        for ($i=0; $i < $count; $i++) {
            $checkBiller = $this->billerGetTrashData($id[$i]);

            // Validasi Data Biller
            if (false == $checkBiller) :
                $notFound[] = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response Biller Not Found
        if (null == $count) :
            return $this->billerNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) {
                $data = $this->billerGetTrashData($id[$n]);
                $data->CSC_BILLER_DELETED_BY = null;
                $data->CSC_BILLER_DELETED_DT = null;
                $data->save();
            }
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Biller Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(202, 'Restore Data Biller Success But Some Data Not Found', $response);
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Biller Success');
    }

    public function billerByGop(Request $request)
    {
        try {
            $request->validate(
                [
                    'gop' => ['required', 'string', 'max:50'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                '400',
                'invalid Data Validation',
                $th->validator->errors()
            );
        }

        // inisialisasi Variable
        $gop = (false == $request->gop) ? null : $request->gop;

        // Cek GOP
        if (null != $gop) :
            $cekGop = CoreGroupOfProduct::modul($gop)->first('CSC_GOP_PRODUCT_PARENT_PRODUCT');
            if (null == $cekGop) {
                return $this->generalResponse(
                    404,
                    'Data Group of Product Not Found'
                );
            }
        endif;

        // Logic Get Data
        $data = CoreBiller::join(
            'CSCCORE_GROUP_OF_PRODUCT AS GOP',
            'GOP.CSC_GOP_PRODUCT_GROUP',
            '=',
            'CSC_BILLER_GROUP_PRODUCT',
        )
        ->distinct()
        ->where(
            function ($query) use ($gop) {
                if (null != $gop) :
                    $query->where('GOP.CSC_GOP_PRODUCT_PARENT_PRODUCT', $gop);
                endif;
            }
        )
        ->get(
            [
                'CSC_BILLER_ID AS ID',
                'CSC_BILLER_NAME AS NAME',
                'CSC_BILLER_GROUP_PRODUCT AS BILLER_GROUP_PRODUCT'
            ]
        );

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                '200',
                'Get List Biller by Group Of Product Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data List Biller by Group Of Product Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get List Biller by Group Of Product Failed'
            );
        }
    }

    public function billerListModul($config)
    {
        // Logic Simple Config
        if ('simple' == $config) {
            // Logic Get Data
            $data = CoreGroupOfProduct::distinct()
            ->get('CSC_GOP_PRODUCT_PARENT_PRODUCT AS MODUL');

            // Response Sukses
            if (null != count($data)) {
                return $this->generalDataResponse(
                    '200',
                    'Get List Biller Modul Success',
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data List Biller Modul Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Biller Modul Failed'
                );
            }

        // Logic Detail Config
        } elseif ('detail' == $config) {
            // Logic Get Data
            $data = CoreGroupOfProduct::distinct()
            ->paginate(
                10,
                [
                    'CSC_GOP_PRODUCT_PARENT_PRODUCT AS MODUL'
                ]
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Sukses
            if (null != count($data)) {
                return $this->generalDataResponse(
                    '200',
                    'Get List Biller Modul Success',
                    $data
                );
            }

            // Response Not Found
            if (null == count($data)) {
                return $this->generalResponse(
                    404,
                    'Data List Biller Modul Not Found'
                );
            }

            // Response Failed
            if (!$data) {
                return $this->generalResponse(
                    500,
                    'Get List Biller Modul Failed'
                );
            }
        }
    }

    public function unmappingProfile(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(['items' => ['numeric', 'digits_between:1,8']]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic get Data
        try {
            $data = CoreBiller::select(
                'CSC_BILLER_ID AS BILLER_ID',
                'CSC_BILLER_NAME AS BILLER_NAME',
            )
            ->whereNotExists(function ($query) {
                $query->from('CSCCORE_PROFILE_FEE AS PF')
                ->select('PF.CSC_PROFILE_ID')
                ->whereNull('PF.CSC_PROFILE_DELETED_DT')
                ->whereColumn('PF.CSC_PROFILE_ID', 'CSC_BILLER_PROFILE');
            })
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Unmapping Profile-Biller Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $count = count($data);

        // Response Not Found
        if (null == $count) :
            return $this->responseNotFound('Data Unmapping Profile-Biller Not Found');
        endif;

        // Add Inde Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->generalDataResponse(200, 'Get List Unmapping Profile-Biller Success', $data);
    }

    public function updateProfile(Request $request, $billerId)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'profile' => ['required', 'string', 'max:50'],
            ]);

            // Validasi Biller Id
            if (Str::length($billerId) > 5) :
                $warning['biller_id'] = 'The biller_id must not be greater than 5 characters.';
                return $this->invalidValidation($warning);
            endif;
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Varriable
        $profile = $request->profile;

        // Check Data Biller
        $checkBiller = $this->billerById($billerId);

        // Response Biller Not Found
        if (false == $checkBiller) :
            return $this->billerNotFound();
        endif;

        // Check Data Profile
        $checkProfile = CoreProfileFee::searchData($profile)->first();

        // Response Profile Not Found
        if (null == $checkProfile) :
            return $this->responseNotFound('Data Profile Fee Not Found');
        endif;

        // Logic Update Data Biller Profile
        try {
            $data = $this->billerById($billerId);
            $data->CSC_BILLER_PROFILE = $profile;
            $data->save();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Update Data Profile on Biller Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Update Data Profile on Biller Success');
    }
}
