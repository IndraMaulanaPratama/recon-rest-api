<?php

namespace App\Http\Controllers\Api\CID;

use App\Http\Controllers\Controller;
use App\Models\CoreDownCentral;
use App\Models\CorePartner;
use App\Models\CorePartnerCid;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class CorePartnerCidCotroller extends Controller
{
    use ResponseHandler;

    public function getSimpleField()
    {
        return [
            'CSC_PC_ID AS ID',
            'CSC_PC_CID AS CID',
            'CSC_PC_CID AS CID_NAME'
        ];
    }

    public function cekPartner($id)
    {
        return CorePartner::searchData($id)->first();
    }

    public function cekCid($id)
    {
        return CoreDownCentral::searchData($id)->first('CSC_DC_ID');
    }

    public function index(Request $request)
    {
        // Validasi Data Mandatory
        try {
            // Validasi Data Mandatory
            $request->validate(['partner' => ['required', 'string', 'max:50']]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variabel
        $items = ($request->items != null) ? $request->items : 10;
        $partner = $request->partner;
        $cid = [];

        // Cek Data Partner
        $cekCid = self::cekPartner($partner);
        if ($cekCid == null) {
            return $this->responseNotFound('Data Partner Not Found');
        }

        // Logic Get Data CID Partner
        $data = CorePartnerCid::join(
            'CSCCORE_PARTNER AS PARTNER',
            'PARTNER.CSC_PARTNER_ID',
            '=',
            'CSC_PC_PARTNER'
        )
        ->where('PARTNER.CSC_PARTNER_ID', $partner)
        ->paginate(
            $items = $items,
            $column = self::getSimpleField()
        );

        // Hitung Jumlah Data
        $countData = count($data);

        // Get Data CID Based On Data Partner CID
        for ($i=0; $i < $countData; $i++) {
            $idCid = $data[$i]->CID;

            // Logic Get Data CID
            $cid[] = CoreDownCentral::select(
                'CSC_DC_NAME AS CID_NAME'
            )
            ->searchData($idCid)
            ->first();
        }

        // Mapping And Change CID NAME VALUE
        for ($i=0; $i < $countData; $i++) {
            $data[$i] = collect($data[$i]);
            $data[$i]->put('CID_NAME', $cid[$i]['CID_NAME']);
        }

        // Response Sukses
        if (null != count($data)) {
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(200, 'Get List CID-Partner Success', $data);
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->responseNotFound('Data CID-Partner Not Found');
        }

        // Response Failed
        if (!$data) {
            return $this->failedResponse('Get List CID-Partner Failed');
        }
    }

    public function store(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(
                [
                    'partner' => ['required', 'string', 'max:50'],
                    'cid' => ['required', 'array', 'min:1'],
                    'cid.*' => ['string', 'max:7'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variabel yang dibutuhkan
        $partner = $request->partner;
        $cid = $request->cid;
        $countCid = count($cid);
        $warningNotRegistered = [];
        $warningExists = [];

        // Cek Data Partner
        $cekPartner = self::cekPartner($partner);
        if ($cekPartner == null) {
            return $this->responseNotFound('Data Partner Not Found');
        }

        // Logic Check Data CID
        for ($i=0; $i < $countCid; $i++) {
            $dataCid[] = CoreDownCentral::select(
                'CSC_DC_ID',
                'CSC_DC_NAME',
            )
            ->searchData($cid[$i])
            ->first();

            // Filtrasi Data Cid Yang Tidak Ditemukan
            if (null == $dataCid[$i]) :
                $warningNotRegistered[] = $cid[$i];
                unset($cid[$i]);
                unset($dataCid[$i]);
            endif;
        }

        // Recounting & Reordering Data Cid
        $cid = array_values($cid);
        $dataCid = array_values($dataCid);
        $countCid = count($cid);

        // Logic Check Duplicated Data Partner CID
        for ($i=0; $i < $countCid; $i++) {
            $checkPartner[] = CorePartnerCid::checkData($partner, $cid[$i])->first('CSC_PC_ID');

            // Filtrasi Data Duplicate Partner CID
            if (null != $checkPartner[$i]) :
                $warningExists[] = $cid[$i];
                unset($cid[$i]);
                unset($checkPartner[$i]);
            endif;
        }

        // Recounting & Reordering Data Cid & Partner
        $cid = array_values($cid);
        $checkPartner = (isset($checkPartner) ? array_values($checkPartner) : null);
        $countCid = count($cid);

        // Logic Insert Data Partner CID
        if (null != $countCid) :
            // Inisialisasi Field Insert Data
            $field = [
                'CSC_PC_ID' => Uuid::uuid4(),
                'CSC_PC_PARTNER' => $partner,
            ];
            for ($i=0; $i < $countCid; $i++) {
                $data = CorePartnerCid::create(
                    [
                        'CSC_PC_ID' => Uuid::uuid4(),
                        'CSC_PC_PARTNER' => $partner,
                        'CSC_PC_CID' => $cid[$i],
                    ]
                );

                if (!$data) :
                    return $this->failedResponse('Insert Data CID-Partner Failed');
                endif;
            }
        endif;

        // Response Sukses
        if ($warningExists == null && $warningNotRegistered == null) {
            return $this->generalResponse(200, 'Insert Data CID-Partner Success');

        // Response CID Cannot Processed
        } elseif (null != $warningExists && null != $warningNotRegistered) {
            $response['cid_exists'] = $warningExists;
            $response['cid_not_registered'] = $warningNotRegistered;

            return $this->generalDataResponse(
                202,
                'Insert Data CID-Partner Success But Some CID Cannot Processed',
                $response
            );

        // Response CID Exists
        } elseif (null != $warningExists) {
            $response['cid_exists'] = $warningExists;

            return $this->generalDataResponse(
                202,
                'Insert Data CID-Partner Success But Some CID Exists',
                $response
            );

        // Response CID Not Registered
        } elseif (null != $warningNotRegistered) {
            $response['cid_not_registered'] = $warningNotRegistered;

            return $this->generalDataResponse(
                202,
                'Insert Data CID-Partner Success But Some CID Not Registered',
                $response
            );

        // Response Failed
        } else {
            return $this->failedResponse('Insert Data CID-Partner Failed');
        }
    }

    public function update(Request $request)
    {
        try {
            // Validasi Data Mandatory
            $request->validate(
                [
                    'cid' => ['required', 'string', 'max:7'],
                    'partner' => ['required', 'string', 'max:50'],
                ]
            );

            // Inisialisasi Variable yang dibutuhkan
            $cid = $request->cid;
            $partner = $request->partner;

            // Cek Data CID
            $cekCid = $this->cekCid($cid);
            if (null == $cekCid) {
                return $this->generalResponse(
                    404,
                    'Data CID Not Found'
                );
            }

            // Cek Data Partner
            $cekPartner = CorePartner::searchData($partner)->first('CSC_PARTNER_ID');
            if (null == $cekPartner) {
                return $this->generalResponse(
                    404,
                    'Data Partner Not Found'
                );
            }

            // Logic Update Data Partner
            $update = CorePartnerCid::searchByCid($cid)->first();
            $update->CSC_PC_PARTNER = $partner;
            $update->save();

            // Response Sukses
            if ($update) {
                return $this->generalResponse(
                    200,
                    'Insert Data Partner on CID Success'
                );
            }

            // response Failed
            if (!$update) {
                return $this->generalResponse(
                    500,
                    'Insert Data Partner on CID Failed'
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

    public function destroy($id)
    {
        // Validasi Data Mandatory
        if (null != $id) {
            try {
                // Response Invalid Id Required
                if (Str::length($id) > 36) {
                    $id = ['The id must not be greater than 36 characters.'];

                    return $this->generalDataResponse(
                        400,
                        'Invalid Data Validation',
                        $id
                    );
                }

                // Cek Data Partner
                $data = CorePartnerCid::searchData($id)->first();
                if (null == $data) {
                    return $this->generalResponse(
                        404,
                        'Data CID-Partner Not Found'
                    );
                }

                // Logic Delete Data
                $data = CorePartnerCid::searchData($id)->delete();

                // Reponse Sukses
                if ($data) {
                    return $this->generalResponse(
                        200,
                        'Delete Data CID-Partner Success'
                    );
                }

                // Response Failed
                if (!$data) {
                    return $this->generalResponse(
                        500,
                        'Delete Data CID-Partner Failed'
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

        // Reponse Invalid Data Mandatory
        if (null == $id) {
            return $this->generalResponse(
                400,
                'Invalid Data Validation'
            );
        }
    }

    public function unmappingCid(Request $request)
    {
        // Inisialisasi Variable yang dibituhkan
        $items = ($request->items != null) ? $request->items : 10;

        // Logic Data Unmapping CID
        $data = CoreDownCentral::select(
            'CSC_DC_ID AS CID',
            'CSC_DC_NAME AS CID_NAME'
        )
        ->whereNotExists(function ($query) {
            $query->from('VSI_DEVEL_REPORT.CSCCORE_PARTNER_CID AS PC')
             ->select('CSC_PC_CID')
             ->whereColumn('CSC_DC_ID', 'PC.CSC_PC_CID');
        })
        ->paginate($items);

        // Response Sukses
        if (null != count($data)) {
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(
                200,
                'Get Data Unmapping Partner-CID Success',
                $data
            );
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Unmapping Partner-CID Not Found'
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                404,
                'Data Unmapping Partner-CID Failed'
            );
        }
    }

    public function addUnmappingCID(Request $request)
    {
        try {
            // Validasi Data Mandatory
            $request->validate(
                [
                    'cid' => ['required', 'string', 'max:7'],
                    'partner' => ['required', 'string', 'max:50'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable yang dibutuhkan
        $cid = $request->cid;
        $partner = $request->partner;
        $keterangan = null;

        // Cek Data CID
        $cekCid = $this->cekCid($cid);
        if (null == $cekCid) {
            return $this->responseNotFound('Data CID Not Found');
        }

        // Cek Data Partner
        $cekPartner = CorePartner::searchData($partner)->first('CSC_PARTNER_ID');
        if (null == $cekPartner) {
            return $this->responseNotFound('Data Partner Not Found');
        }

        // Cek Data Unmapping CID
        $cekData = CorePartnerCid::checkData($partner, $cid)->first();
        if (null != $cekData) {
            return $this->generalResponse(
                409,
                'Data Unmapping CID-Partner Exists',
            );
        }

        // Validasi Duplikat UUID
        while ($keterangan == false) {
            $id = Uuid::uuid4();
            $cekId = CorePartnerCid::searchData($id)->first();

            if (null == $cekId) {
                $keterangan = true;
            } else {
                $keterangan = false;
            }
        }

        // Logic Add Data Unmapping
        $store = CorePartnerCid::create(
            [
                'CSC_PC_ID' => $id,
                'CSC_PC_CID' => $cid,
                'CSC_PC_PARTNER' => $partner,
            ]
        );

        // Response Sukses
        if ($store) {
            return $this->generalResponse(
                200,
                'Insert Data Unmapping CID-Partner Success'
            );
        }

        // Response Failed
        if (!$store) {
            return $this->generalResponse(
                500,
                'Insert Data Unmapping CID-Partner Failed'
            );
        }
    }
}
