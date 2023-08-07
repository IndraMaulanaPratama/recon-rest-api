<?php

namespace App\Http\Controllers\Api\Exlude_Partner;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\PaginateResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\CoreDownCentral;
use App\Models\ExludePartner;
use App\Models\TransactionDefinitionV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CoreExludePartnerController extends Controller
{
    public function getField()
    {
        return [
            'CSC_EP_ID AS ID',
            'CSC_EP_CID AS CID',
            'CSC_EP_PRODUCT AS PRODUCT',
        ];
    }

    public function getPaginate()
    {
        return [
            'CSC_EP_ID AS ID',
            'CSC_EP_CID AS CID',
            'CSC_DC_NAME AS CID_NAME',
            'CSC_EP_PRODUCT AS PRODUCT',
        ];
    }

    public function index(Request $request, $config)
    {
        if ('simple' == $config) {
            $data = ExludePartner::get(self::getField());

            if (null != count($data)) {
                return response()->json(
                    new PaginateResponseResource(
                        200,
                        'Get List Exclude Partner Success',
                        $config,
                        $data
                    ),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Exclude Partner Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        } elseif ('detail' == $config) {
            $items = (null != $request->items) ? $request->items : 10;

            $data = DB::connection('server_report')->table('CSCCORE_EXCLUDE_PARTNER as exclude_partner')
            ->join(
                'CSCCORE_DOWN_CENTRAL_GROUP as down_central_group',
                'down_central_group.CSC_DC_ID',
                '=',
                'exclude_partner.CSC_EP_CID'
            )
            ->paginate(
                $perpage = $items,
                $column = self::getPaginate()
            );

            // Add Index Number
            $data = $this->addIndexNumber($data);


            if (false != count($data)) {
                return response(
                    new PaginateResponseResource(200, 'Get List Exclude Partner Success', $config, $data),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response(
                new ResponseResource(404, 'Data Exclude Partner Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Exclude Partner Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }

    public function store(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(
                [
                    'cid' => ['required'],
                    'cid.*' => ['required', 'string', 'max:7'],
                    'product' => ['required'],
                    'product.*' => ['required', 'string', 'max:100'],
                ]
            );
        } catch (ValidationException $th) {
            return response()->json(
                new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                400
            );
        }

        // Inisialisasi Variable
        $cid = $request->cid;
        $countCid = count($cid);
        $warninRegistered = [];
        $warninExist = [];
        $warninCid = [];

        // Logic Get Data
        for ($i = 0; $i < $countCid; ++$i) {
            $cekCid[$i] = CoreDownCentral::searchData($cid)->first('CSC_DC_ID');

            if (null == $cekCid[$i]) {
                $warninCid[] = $cid[$i];
                unset($cekCid[$i]);
            } else {
                $product = $request->product;
                $countApi = count($product);

                for ($n = 0; $n < $countApi; ++$n) {
                    $cekData[$n] = TransactionDefinitionV2::searchData($product[$n])
                    ->first(); // null is true

                    $cekExist[$n] = ExludePartner::searchDataByClient($cid[$i], $product[$n])
                    ->first(); // null is true

                    if (null == $cekData[$n] && null == $cekExist[$n]) {
                        $warninRegistered[] = $product[$n];
                        unset($product[$n]);
                    } elseif (null != $cekData[$n] && null != $cekExist[$n]) {
                        $warninExist[] = ['cid' => $cekCid[$i]->CID, 'product' => $cekExist[$n]->CSC_EP_PRODUCT];
                        unset($product[$n]);
                    } elseif (null == $cekData[$n] && null != $cekExist[$n]) {
                        $warninRegistered[] = $product[$n];
                        $warninExist[] = ['cid' => $cekCid[$i]->CID, 'product' => $cekExist[$n]->CSC_EP_PRODUCT];
                        unset($product[$n]);
                    } else {
                        $keterangan = null;
                        while ($keterangan == false) {
                            $id = Uuid::uuid4();
                            $cekId = ExludePartner::searchData($id)->first();

                            if (null == $cekId) {
                                $keterangan = true;
                            } else {
                                $keterangan = false;
                            }
                        }
                        ExludePartner::create([
                            'CSC_EP_ID' => $id,
                            'CSC_EP_CID' => $cid[$i],
                            'CSC_EP_PRODUCT' => $product[$n],
                        ]);
                    }
                }
            }
        }

        // Response Success
        if (null == $warninRegistered && null == $warninExist && null == $warninCid) {
            return response()->json(
                new ResponseResource(200, 'Insert Data Exclude Partner Success'),
                200
            );
        }

        // Response Cannot Process
        if (null != $warninRegistered || null != $warninExist || null != $warninCid) {
            $status = 202;
            $message = 'Insert Data Exclude Partner Success But Some Data Cannot Processed';
            (null == $warninCid) ?: $response['cid_not_registered'] = $warninCid;
            (null == $warninExist) ?: $response['data_exists'] = $warninExist;
            (null == $warninRegistered) ?: $response['product_not_registered'] = $warninRegistered;

            return $this->generalDataResponse(
                $status,
                $message,
                $response
            );
        }

        // Response Failed
        return $this->failedResponse('Insert Data Exclude Partner Failed');
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

                $data = ExludePartner::searchData($id)->first();
                if (null == $data) {
                    return response()->json(
                        new ResponseResource(404, 'Data Exclude Partner Not Found'),
                        404,
                    );
                }

                $data->delete();

                if ($data) {
                    return response()->json(
                        new ResponseResource(200, 'Delete Data Exclude Partner Success'),
                        200,
                    );
                }

                return response()->json(
                    new ResponseResource(500, 'Delete Data Exclude Partner Failed'),
                    500,
                );
            } catch (ValidationException $th) {
                return response()->json(
                    new DataResponseResource(400, 'Invalid Data Validation', $th->validator->errors()),
                    400
                );
            }
        } else {
            return response()->json(
                new ResponseResource(400, 'Invalid Data Validation'),
                400
            );
        }
    }

    public function filter(Request $request)
    {
        // ?cid=&cid_name=&product=&items=50
        $items = (null != $request->items) ? $request->items : 10;
        $data = DB::connection('server_report')->table('CSCCORE_EXCLUDE_PARTNER as exclude_partner')
        ->join(
            'CSCCORE_DOWN_CENTRAL_GROUP as down_central_group',
            'down_central_group.CSC_DC_ID',
            '=',
            'exclude_partner.CSC_EP_CID'
        )
        ->where(
            function ($query) use ($request) {
                if (null != $request->cid) {
                    $query->where('CSC_EP_CID', 'LIKE', '%'. $request->cid. '%');
                }

                if (null != $request->cid_name) {
                    $query->where('down_central_group.CSC_DC_NAME', 'LIKE', '%'. $request->cid_name.'%');
                }

                if (null != $request->product) {
                    $query->where('CSC_EP_PRODUCT', 'LIKE', '%'. $request->product.'%');
                }
            }
        )

        ->paginate(
            $perpage = $items,
            $column = self::getPaginate()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        if (false != count($data)) {
            return response(
                new DataResponseResource(200, 'Filter Data Exclude Partner Success', $data),
                200,
                ['Accept' => 'Application/json']
            );
        }

        return response(
            new ResponseResource(404, 'Data Exclude Partner Not Found'),
            404,
            ['Accept' => 'Application/json']
        );
    }
}
