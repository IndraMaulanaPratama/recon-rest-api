<?php

namespace App\Http\Controllers\Api\ReconDana;

use App\Exports\ReconDana\MultiBiller\ExportMultiBiller;
use App\Http\Controllers\Controller;
use App\Models\CoreBiller;
use App\Models\CoreBillerCollection;
use App\Models\CoreCorrection;
use App\Models\CoreProductFunds;
use App\Models\CoreReconData;
use App\Models\CoreReconDataHistory;
use App\Models\ReconDana;
use App\Models\TransactionDefinitionV2;
use App\Models\TrxCorrection;
use Illuminate\Http\Request;
use App\Traits\ResponseHandler;
use App\Traits\ReconDanaTraits;
use Illuminate\Validation\ValidationException;
use App\Traits\BillerTraits;
use App\Traits\CidTraits;
use App\Traits\CorrectionTraits;
use App\Traits\GroupBillerTraits;
use App\Traits\GroupTransferTraits;
use App\Traits\ProductFundsTraits;
use Ramsey\Uuid\Uuid;
use App\Traits\ProductTraits;
use App\Traits\TrxCorrectionTraits;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class ReconDanaController extends Controller
{
    use ReconDanaTraits;
    use ResponseHandler;
    use BillerTraits;
    use GroupBillerTraits;
    use ProductTraits;
    use GroupTransferTraits;
    use ProductFundsTraits;
    use BillerTraits;
    use CorrectionTraits;
    use CidTraits;
    use TrxCorrectionTraits;

    public function mappingByProduct($data, $mapping)
    {
        $jumlahField = $mapping['jumlah_field'];
        $namaField = $mapping['nama_field'];
        $valueField = $mapping['value_field'];

        for ($map=0; $map < $jumlahField; $map++) {
            $data->put($namaField[$map], $valueField[$map]);
        }
    }

    public function unmappingBiller(Request $request)
    {
        // Inisialisasi Variable
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Data
        $data = CoreBiller::getData()
        ->whereNotExists(function ($query) {
            $query->select('BC.CSC_BC_BILLER')
            ->from('CSCCORE_BILLER_COLLECTION AS BC')
            ->whereColumn('CSC_BILLER_ID', 'BC.CSC_BC_BILLER');
        })
        ->paginate(
            $items,
            $column = [
                'CSC_BILLER_ID AS BILLER_ID',
                'CSC_BILLER_GROUP_PRODUCT AS GROUP_PRODUCT',
                'CSC_BILLER_NAME AS BILLER_NAME',
            ]
        );

        // Response Not Found
        $countData = count($data);
        if (null == $countData) :
            return $this->reconDanaUnmappingBillerNotFound();
        endif;

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Get Data Unmapping Group Biller-Biller Failed');
        endif;

        // Response Success
        if ($data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(200, 'Get Data Unmapping Group Biller-Biller Success', $data);
        endif;
    }

    public function addBiller(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'biller_id' => ['required', 'string', 'max:5'],
                'group_biller' => ['required', 'string', 'max:20'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $biller = $request->biller_id;
        $group = $request->group_biller;

        // Cek Data Biller
        $cekBiller = $this->billerById($biller);
        if (false == $cekBiller) :
            return $this->billerNotFound();
        endif;

        // Cek Data Group Biller
        $cekGroupBiller = $this->groupBillerGetData($group);
        if (false == $cekGroupBiller) :
            return $this->groupBillerNotFound();
        endif;

        // Cek Group Biller Exists
        $cekExists = $this->groupBillerCheckExists($biller, $group);
        if (false == $cekExists) :
            return $this->groupBillerCollectionExists();
        endif;

        // Inisialisasi field Insert
        $field = [
            'CSC_BC_ID' => Uuid::uuid4(),
            'CSC_BC_GROUP_BILLER' => $group,
            'CSC_BC_BILLER' => $biller
        ];

        // Logic Add Data
        $data = CoreBillerCollection::create($field);

        // Response Success
        if ($data) :
            return $this->generalResponse(200, 'Insert Data Group Biller-Biller Success');
        endif;

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Insert Data Group Biller-Biller Failed');
        endif;
    }

    public function unmappingProduct(Request $request)
    {
        // inisialisasi Variable
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Data
        $data = TransactionDefinitionV2::getData()
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNotExists(function ($query) {
            $query->from('CSCCORE_PRODUCT_FUNDS AS PF')
            ->join(
                'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
                'GTF.CSC_GTF_ID',
                '=',
                'PF.CSC_PF_GROUP_TRANSFER'
            )
            ->select('GTF.CSC_GTF_ID', 'PF.CSC_PF_PRODUCT')
            ->whereColumn('CSC_TD_NAME', 'PF.CSC_PF_PRODUCT')
            ->whereColumn('GTF.CSC_GTF_ID', 'PF.CSC_PF_GROUP_TRANSFER');
        })
        ->paginate(
            $items,
            $column = [
                'B.CSC_BILLER_NAME AS BILLER',
                'CSC_TD_NAME AS PRODUCT'
            ]
        );

        // Hitung Data
        $countData = count($data);

        // Response Not Found
        if (null == $countData) :
            return $this->reconDanaUnmappingProductNotFound();
        endif;

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Get Data Unmapping Group Transfer-Product Failed');
        endif;

        // Response Success
        if ($data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);
            return $this->generalDataResponse(200, 'Get Data Unmapping Group Transfer-Product Success', $data);
        endif;
    }

    public function addProduct(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate([
                'product' => ['required', 'string', 'max:100'],
                'group_transfer' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $product = $request->product;
        $group = $request->group_transfer;

        // Cek Data Product
        $cekProduct = $this->productCheckData($product);
        if (false == $cekProduct) :
            return $this->productNotFound();
        endif;


        // Cek Group Transfer
        $cekGroup = $this->groupTransferById($group);
        if (false == $cekGroup) :
            return $this->groupTransferNotFound();
        endif;

        // Check Exists
        $cekExists = $this->productFundsCheckExists($product, $group);
        if (false == $cekExists) :
            return $this->generalResponse(409, 'Insert Data Group Transfer-Product Exists');
        endif;

        // Inisialisasi Field Create Data
        $field = [
            'CSC_PF_ID' => Uuid::uuid4(),
            'CSC_PF_PRODUCT' => $product,
            'CSC_PF_GROUP_TRANSFER' => $group,
        ];

        // Logic Create Data
        $data = CoreProductFunds::create($field);

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Insert Data Group Transfer-Product Failed');
        endif;

        // Response Success
        if ($data) :
            return $this->generalResponse(200, 'Insert Data Group Transfer-Product Success');
        endif;
    }

    public function process(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate([
                "user_process" => ['required', 'string', 'max:50'],
                "id" => ["required", 'array', 'min:1'],
                "id.*" => ['string', 'max:36'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $user = $request->user_process;
        $countId = count($id);
        $data = [];
        $cannotProcess = [];
        $idNotRegistered = [];

        // Logic Get Data
        for ($i=0; $i < $countId; $i++) {
            $data[] = ReconDana::select(
                'CSC_RDN_ID AS ID',
                'CSC_RDN_STATUS AS STATUS',
                'BILLER.CSC_BILLER_NAME AS BILLER',
                'GTF.CSC_GTF_NAME AS GROUP_TRANSFER'
            )
            ->join(
                'CSCCORE_BILLER AS BILLER',
                'CSC_RDN_BILLER',
                '=',
                'BILLER.CSC_BILLER_ID'
            )
            ->whereNull('BILLER.CSC_BILLER_DELETED_DT')
            ->join(
                'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
                'CSC_RDN_GROUP_TRANSFER',
                '=',
                'GTF.CSC_GTF_ID'
            )
            ->whereNull('GTF.CSC_GTF_DELETED_DT')
            ->id($id[$i])
            ->first();

            if (null == $data[$i]) :
                $idNotRegistered[]['id'] = $id[$i];
                unset($id[$i]);
                unset($data[$i]);
            endif;
        }

        // Recounting Data ID And Data
        $id = array_values($id);
        $data = array_values($data);
        $countId = count($id);

        // Response Not Found
        if (null == $countId) :
            return $this->reconDanaNotFound();
        endif;

        // Hitung data yang ditemukan
        $countData = count($data);

        // Inisialisasi Status And Filtering by statusData Recon
        for ($i=0; $i < $countData; $i++) {
            // *** Inisialisasi Status *** //
            // Status 1
            if (1 == $data[$i]['STATUS']) :
                $status = "Waiting Process";
            endif;

            // Status 0
            if (0 == $data[$i]['STATUS']) :
                $status = "Closed";
            endif;

            // Status 2
            if (2 == $data[$i]['STATUS']) :
                $status = "Processed";
            endif;

            // Status 3
            if (3 == $data[$i]['STATUS']) :
                $status = "Released";
            endif;

            $data[$i] = collect($data[$i]);
            $data[$i]->put('STATUS', $status);
            // *** END OF INISIALISASI *** //

            // *** GROUPING STATUS *** //
            // Status 0
            if ("Closed" == $data[$i]['STATUS']) :
                $cannotProcess[] = $data[$i];
            endif;

            // Status 2
            if ("Processed" == $data[$i]['STATUS']) :
                $cannotProcess[] = $data[$i];
            endif;

            // Status 3
            if ("Released" == $data[$i]['STATUS']) :
                $cannotProcess[] = $data[$i];
            endif;

            // Removing Data yang tidak terpakai
            if ("Waiting Process" != $data[$i]['STATUS']) :
                unset($data[$i]);
                unset($id[$i]);
            endif;
            // *** END OF GROUPING STATUS *** ///
        }

        // return response()->json([
        //     'not_registered' => $idNotRegistered,
        //     'cannot_process' => $cannotProcess,
        //     'processable' => $data,
        // ]);

        // Recounting Data ID And Data
        $id = array_values($id);
        $data = array_values($data);
        $countId = count($id);

        // Process Update Status
        for ($i=0; $i < $countId; $i++) {
            $update[] = ReconDana::id($id[$i])
            ->status(1)
            ->first();

            $update[$i]->CSC_RDN_STATUS = 2;
            $update[$i]->CSC_RDN_USER_PROCESS = $user;
            $update[$i]->save();
        }

        // Response Failed
        if (!$update) :
            return $this->generalResponse(200, 'Recon Dana Process Failed');
        endif;

        // Response Success
        if ($update
        && null == $idNotRegistered
        && null == $cannotProcess) :
            return $this->generalResponse(200, 'Recon Dana Process Success');
        endif;

        // Response Success With Id Not Registered
        if ($update
        && null != $idNotRegistered
        && null == $cannotProcess) :
            $response = ['id_not_registered' => $idNotRegistered];
            return $this->generalDataResponse(
                202,
                'Recon Dana Process Success but Some ID Not Registered',
                $response
            );
        endif;

        // Response Success With Data Cannot Registered
        if ($update
        && null == $idNotRegistered
        && null != $cannotProcess) :
            $response = ['recon_dana_cannot_processed' => $cannotProcess];
            return $this->generalDataResponse(
                202,
                'Recon Dana Process Success but Some Recon Dana Cannot Processed',
                $response
            );
        endif;

        // Response Success With Data Cannot Processed
        if ($update
        && null != $idNotRegistered
        && null != $cannotProcess) :
            $response['id_not_registered'] = $idNotRegistered;
            $response['recon_dana_cannot_processed' ] = $cannotProcess;
            return $this->generalDataResponse(
                202,
                'Recon Dana Process Success but Some Recon Dana Cannot Processed',
                $response
            );
        endif;
    }

    public function listCorrectionProcess(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'biller_id' => ['required', 'string', 'max:5'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $biller = $request->biller_id;
        $items = (null == $request->items) ? 10 : $request->items;

        // Check Data Biller
        $checkBiller = $this->billerById($biller);
        if (false == $checkBiller) :
            return $this->billerNotFound();
        endif;

        // Logic Get Data
        $data = $this->reconDanaCorrectionList($biller, $items);

        // Response Not Found
        if (false == $data) :
            return $this->responseNotFound('Data Correction Settled Not Found');
        endif;

        // Hitung Junlah Data
        $countData = count($data);

        // Penyesuaian Amount
        for ($i=0; $i < $countData; $i++) {
            if ('-' == $data[$i]->TYPE) :
                $data[$i] = collect($data[$i]);
                $data[$i]->put('AMOUNT', -1 * $data[$i]['AMOUNT']);
                $data[$i]->forget('TYPE');
            else :
                $data[$i] = collect($data[$i]);
                $data[$i]->forget('TYPE');
            endif;
        }

        // Response Failed
        if (!$data) :
            return $this->responseFailed('Get Data Correction Settled Failed');
        endif;

        // Response Success
        if ($data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);
            return $this->generalDataResponse(200, 'Get Data Correction Settled Success', $data);
        endif;
    }

    public function updateCorrectionProcess(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'recon_id' => ['required', 'string', 'max:36'],
                'id' => ['required', 'array', 'min:1'],
                'id.*' => ['string', 'max:36'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $reconId = $request->recon_id;
        $countId = count($id);
        $data = [];
        $dataNotFound = [];
        $correctionValue = null;

        // Check Data Recon Id
        $checkReconDana = $this->reconDanaById($reconId);
        if (false == $checkReconDana) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get Recon Dana
        $dana = ReconDana::select(
            'CSC_RDN_ID',
            'CSC_RDN_CORRECTION_PROCESS',
            'CSC_RDN_CORRECTION_PROCESS_VALUE',
            'CSC_RDN_AMOUNT_TRANSFER',
        )
        ->id($reconId)
        ->where('CSC_RDN_STATUS', 1)
        ->first();

        // Inisialisasi Nilai Correction di table recon dana
        if ('-' == $dana->CSC_RDN_CORRECTION_PROCESS_VALUE) :
            $danaCorrection = $dana->CSC_RDN_CORRECTION_PROCESS * -1;
        else :
            $danaCorrection = $dana->CSC_RDN_CORRECTION_PROCESS;
        endif;

        // Logic Check Data Correction
        for ($i=0; $i < $countId; $i++) {
            $data[$i] = $this->correctionById($id[$i]);

            if (false == $data[$i]) :
                $dataNotFound[] = $id[$i];
                unset($id[$i]);
                unset($data[$i]);
            else :
                // Logic Summary nilai correction di table correction
                $correction = $data[$i]['CSC_CORR_CORRECTION'];
                $operator = $data[$i]['CSC_CORR_CORRECTION_VALUE'];
                if ('+' == $operator) :
                    $correctionValue = $correctionValue + $correction;
                else :
                    $correctionValue = $correctionValue - $correction;
                endif;
            endif;
        }

        // Inisialisasi Value Amount Transfer Recon Dana
        $amountTransfer =  $dana->CSC_RDN_AMOUNT_TRANSFER;
        $correctionAmount =  $correctionValue;

        // Logic Inisialisasi Value Update Correction
        $danaCorrection = $correctionValue + $danaCorrection;
        if (0 > $danaCorrection) :
            $danaCorrection = $danaCorrection * -1;
            $correctionValue = '-';
        else :
            $correctionValue = '+';
        endif;

        // return response()->json([
        //     'dana' => $danaCorrection,
        //     'value' => $correctionValue,
        //     'correction_value' => $correctionValue,
        // ]);

        // Reorder Data Id Correction And Data
        $id = array_values($id);
        $data = array_values($data);

        // Recounting Id Correction And Data
        $countId = count($id);
        $countData = count($data);

        // Response Not Found
        if (null == $countData) :
            return $this->correctionNotFound();
        endif;

        // Logic Update Data Correction
        for ($i=0; $i < $countData; $i++) {
            $data[$i]->CSC_CORR_STATUS = 0;
            $data[$i]->CSC_CORR_RECON_DANA_ID = $reconId;
            $data[$i]->CSC_CORR_DATE_PINBUK = Carbon::now('ASIA/JAKARTA')->toDateString();
            $data[$i]->save();

            if (!$data) :
                return $this->failedResponse('Update Data Correction Settled Failed');
            endif;
        }

        // Logic Update Data Recon Dana
        $dana->CSC_RDN_CORRECTION_PROCESS_VALUE = $correctionValue;
        $dana->CSC_RDN_CORRECTION_PROCESS = $danaCorrection;
        $dana->CSC_RDN_AMOUNT_TRANSFER = $amountTransfer + $correctionAmount;
        $dana->save();

        // Hitung Data Not register
        $countNotFound = count($dataNotFound);

        // Response Success With Warning
        if (null != $countNotFound) :
            $response = ['correction_not_registered' => $dataNotFound];
            return $this->generalDataResponse(
                '202',
                'Update Data Correction Settled Success but Some Correction Not Registered',
                $response
            );
        endif;

        // Response Success
        if (null == $countNotFound) :
            return $this->generalResponse(200, 'Update Data Correction Settled Success');
        endif;
    }

    public function listSuspectProcess(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'biller_id' => ['required', 'string', 'max:5'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $biller = $request->biller_id;
        $items = (null == $request->items) ? 10 : $request->items;
        $amount = [];

        // Check Data Biller
        $checkBiller = $this->billerById($biller);
        if (false == $checkBiller) :
            return $this->billerNotFound();
        endif;

        // Logic Get Data
        $data = $this->reconDanaListSuspect($biller, $items);

        // Response Not Found
        if (false == $data) :
            return $this->responseNotFound('Data Suspect Settled Not Found');
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(500, 'Get Data Suspect Settled Failed');
        endif;

        // Hitung Jumlah Data
        $countData = count($data);

        // Logic Get Data Dilunaskan
        $dilunaskan = $this->reconDanaListSuspectByStatus($biller, 0);

        // Logic Get Data Dibatalkan
        $dibatalkan = $this->reconDanaListSuspectByStatus($biller, 1);

        // Logic Hitung Amount -> Mapping data
        for ($i=0; $i < $countData; $i++) {
            $lunas[] = (isset($dilunaskan[$i]->AMOUNT)) ? $dilunaskan[$i]->AMOUNT : 0;
            $batal[] = (isset($dibatalkan[$i]->AMOUNT)) ? $dibatalkan[$i]->AMOUNT : 0;

            if ($lunas[$i] <= $batal[$i]) :
                $amount[$i] = ($batal[$i] - $lunas[$i]) * -1;
            endif;

            if ($lunas[$i] >= $batal[$i] || $lunas[$i] == $batal[$i]) :
                $amount[$i] = $lunas[$i] - $batal[$i];
            endif;

            // Mapping Amount ke Data Paginate
            $data[$i] = collect($data[$i]);
            $data[$i]->put('AMOUNT', $amount[$i]);
        }

        // Response Success
        if ($data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(200, 'Get Data Suspect Settled Success', $data);
        endif;
    }

    public function updateSuspectProcess(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'recon_id' => ['required', 'string', 'max:36'],
                'data' => ['required', 'array', 'min:1'],
                'data.*.product' => ['required', 'string', 'max:100'],
                'data.*.trx_date' => ['required', 'date_format:Y-m-d'],

            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $data = $request->data;
        $reconId = $request->recon_id;
        $countData = count($request->data);
        $productNotFound = [];
        $product = [];
        $date = [];
        $id = [];
        $suspectValue = null;
        $danaSuspect = null;

        // Check Data Recon Id
        $checkReconDana = $this->reconDanaById($reconId);
        if (false == $checkReconDana) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get Recon Dana
        $dana = ReconDana::select(
            'CSC_RDN_ID',
            'CSC_RDN_SUSPECT_PROCESS',
            'CSC_RDN_SUSPECT_PROCESS_VALUE',
            'CSC_RDN_AMOUNT_TRANSFER',
        )
        ->id($reconId)
        ->where('CSC_RDN_STATUS', 1)
        ->first();

        // Inisialisasi Nilai Suspect di table recon dana
        $danaStatus = $dana->CSC_RDN_SUSPECT_PROCESS_VALUE;
        if ('-' == $danaStatus) :
            $danaSuspect = $dana->CSC_RDN_SUSPECT_PROCESS * -1;
        else :
            $danaSuspect = $dana->CSC_RDN_SUSPECT_PROCESS;
        endif;

        // Check Data Product -> Get Data Product & Date
        for ($i=0; $i < $countData; $i++) {
            $checkProduct[$i] = $this->productCheckData($data[$i]['product']);

            // Get Data Product & Date
            if (false == $checkProduct[$i]) :
                $productNotFound[] = $data[$i]['product'];
                unset($data[$i]);
            else :
                $product[] = $data[$i]['product'];
                $date[] = $data[$i]['trx_date'];
            endif;
        }

        // Count Data Product Not Registered
        $countNotRegister = count($productNotFound);

        // Reorder Product & Date
        $product = array_values($product);
        $date = array_values($date);
        $data = array_values($data);

        // Recounting data
        $countData = count($product);

        // Logic Get Data Suspect -> Get Id Suspect
        $data = null;
        for ($i=0; $i < $countData; $i++) {
            $data[] = TrxCorrection::select(
                'CSM_TC_ID',
                'CSM_TC_STATUS_FUNDS',
                'CSM_TC_TRX_DT',
                'CSM_TC_BILLER_AMOUNT',
                'CSM_TC_STATUS_TRX',
            )
            ->product($product[$i])
            ->date($date[$i])
            ->whereNull('CSM_TC_RECON_DANA_ID')
            ->statusFunds(1)
            ->statusData(0)
            ->get();

            // Get Id Suspect
            for ($j=0; $j < count($data[$i]); $j++) {
                $id[] = $data[$i][$j]->CSM_TC_ID;

                // Logic Summary nilai suspect di table suspect
                $status = $data[$i][$j]->CSM_TC_STATUS_TRX;
                $suspect = $data[$i][$j]->CSM_TC_BILLER_AMOUNT;

                // 0/dilunaskan = + | 1/dibatalkan = -
                if (1 == $status) :
                    $suspectValue = $suspectValue - $suspect;
                else :
                    $suspectValue = $suspectValue + $suspect;
                endif;
            }
        }

        $suspectAmount = $suspectValue;
        $amountTransfer = $dana->CSC_RDN_AMOUNT_TRANSFER;

        // Logic Inisialisasi Value Update Suspect
        $danaSuspect = $suspectValue + $danaSuspect;
        if (0 > $danaSuspect) :
            $danaSuspect = $danaSuspect * -1;
            $suspectValue = '-';
        else :
            $suspectValue = '+';
        endif;

        // Logic Update Status Funds
        $data = null;
        $countId = count($id);
        for ($i=0; $i < $countId; $i++) {
            $data[$i] = TrxCorrection::select(
                'CSM_TC_ID',
                'CSM_TC_STATUS_FUNDS',
                'CSM_TC_TRX_DT',
            )
            ->searchData($id[$i])
            ->first();

            $data[$i]->CSM_TC_STATUS_FUNDS = 0;
            $data[$i]->CSM_TC_RECON_DANA_ID = $reconId;
            $data[$i]->save();
        }

        // Logic Update Suspect recon Dana
        $dana->CSC_RDN_SUSPECT_PROCESS = $danaSuspect;
        $dana->CSC_RDN_SUSPECT_PROCESS_VALUE = $suspectValue;
        $dana->CSC_RDN_AMOUNT_TRANSFER = $amountTransfer + $suspectAmount;
        $dana->save();

        // Response Success With Warning
        if (null != $countNotRegister) :
            $response = ['product_not_registered' => $productNotFound];
            return $this->generalDataResponse(
                202,
                'Update Data Suspect Settled Success but Some Product Not Registered',
                $response
            );
        endif;

        // Response Sucess
        if (null == $countNotRegister) :
            return $this->generalResponse(200, 'Update Data Suspect Settled Success');
        endif;
    }

    public function listSummary(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'start' => ['required', 'date_format:Y-m-d'],
                'end' => ['required', 'date_format:Y-m-d'],
                'group_biller' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $start = $request->start;
        $end = $request->end;
        $date = [$request->start, $request->end];
        $group = (0 == $request->group_biller) ? null : $request->group_biller;
        $items = (null == $request->items) ? 10 : $request->items;
        $process = [];
        $unprocess = [];
        $total = [];

        // Check Group Biller
        if (null != $group) :
            $checkGroupBiller = $this->groupBillerById($group);
            if (false == $checkGroupBiller) :
                return $this->groupBillerNotFound();
            endif;
        endif;

        // Logic Get Data
        $data = $this->reconDanaListSummary($group, $date, $items);

        // Response Not Found
        if (false == $data) :
            return $this->responseNotFound('Data List Summary Recon Dana Not Found');
        endif;

        // Hitung Jumlah Data
        $countData = count($data);

        // Logic Get Data Total
        for ($i=0; $i < $countData; $i++) {
            // Logic Get Data Process Total
            $process[] = $this->reconDanaProcessSummary($data[$i]['GROUP_BILLER_ID'], $data[$i]['DATE_TRANSFER']);

            // Logic Get Data Unprocess Total
            $unprocess[] = $this->reconDanaUnprocessSummary($data[$i]['GROUP_BILLER_ID'], $data[$i]['DATE_TRANSFER']);

            $total[$i] = $process[$i]['AMOUNT_TRANSFER'] + $unprocess[$i]['AMOUNT_TRANSFER'];
        }

        // return response()->json([
        //     'data' => $data,
        //     'process' => $process,
        //     'unprocess' => $unprocess,
        // ]);

        // Mapping Date Periode Process, Unprocess, Total To Data Paginate
        for ($i=0; $i < $countData; $i++) {
            // Membuat Field Recon Period
            $reconPeriod[] = $data[$i]['START_PERIOD']. ' to '. $data[$i]['END_PERIOD'];

            $data[$i] = collect($data[$i]);
            $data[$i]->put('RECON_PERIOD', $reconPeriod[$i]);
            $data[$i]->put('PROCESS_TOTAL', $process[$i]['AMOUNT_TRANSFER']);
            $data[$i]->put('UNPROCESS_TOTAL', $unprocess[$i]['AMOUNT_TRANSFER']);
            $data[$i]->put('TOTAL', $total[$i]);
            $data[$i]->forget('START_PERIOD');
            $data[$i]->forget('END_PERIOD');
        }

        // Response Success
        if ($data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(200, 'Get List Summary Recon Dana Success', $data);
        endif;
    }

    public function list(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'settled_dt' => ['required', 'string', 'max:10'],
                'group_biller' => ['required', 'string', 'max:50'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Data Variable
        $date = $request->settled_dt;
        $group = $request->group_biller;
        $items = (null == $request->items) ? 10 : $request->items;
        $total = null;

        // Check Data Group Biller
        $checkGroup = $this->groupBillerById($group);
        if (false == $checkGroup) :
            return $this->groupBillerNotFound();
        endif;

        // Get Data Summary
        try {
            $summary = $this->reconDanaSummary($group, $date);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List Recon Dana Failed', $th->getMessage());
            Log::error('Get Data Summary', $th->getMessage());
        }

        // Response Not Found
        if (false == $summary) :
            return $this->responseNotFound('Data List Recon Dana Not Found');
        endif;

        // Hitung Jumlah Data Summary
        $countSummary = count($summary);

        // Get Data Process and Unprocess Summary
        try {
            $nilai = $this->reconDanaProcessList($date);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List Recon Dana Failed', $th->getMessage());
            Log::error('Get Process and Unprocess', $th->getMessage());
        }

        // Get Process, Unprocess & Total
        for ($i=0; $i < $countSummary; $i++) {
            // Handle Value For Process Amount Transfer
            if ($nilai[$i]['STATUS'] == 1) :
                $process[] = $nilai[$i]['AMOUNT_TRANSFER'];
            else :
                $process[] = 0;
            endif;

            // Handle Value For Unrocess Amount Transfer
            if ($nilai[$i]['STATUS'] == 0 || $nilai[$i]['STATUS'] == 2 || $nilai[$i]['STATUS'] == 3) :
                $unprocess[] = $nilai[$i]['AMOUNT_TRANSFER'];
            else :
                $unprocess[] = 0;
            endif;

            // Get Data Total Summary
            $total[] = $process[$i] + $unprocess[$i];
        }

        // Get Data Total -> Mapping Data Summary
        for ($i=0; $i < $countSummary; $i++) {
            // Mapping data For Response Summary
            $summary[$i] = collect($summary[$i]);
            $summary[$i]->put('SUM_ACCOUNT', $summary[$i]['BANK'] .' - '. $summary[$i]['ACCOUNT_NUMBER']);
            $summary[$i]->put('SUM_PROCESS', $process[$i]);
            $summary[$i]->put('SUM_UNPROCESS', $unprocess[$i]);
            $summary[$i]->put('SUM_TOTAL', $total[$i]);
            $summary[$i]->forget('BANK');
            $summary[$i]->forget('ACCOUNT_NUMBER');
            $summary[$i]->forget('BILLER');
            $summary[$i]->forget('AMOUNT_TRANSFER');
        }

        // Logic Get Data
        try {
            $data = $this->reconDanaList($group, $date, $items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List Recon Dana Failed', $th->getMessage());
            Log::error('Get Data List', $th->getMessage());
        }

        // Hitung Jumlah Data
        $count = count($data);

        // Status Suspect & Correction
        for ($i=0; $i < $count; $i++) {
            $danaId = $data[$i]['ID'];

            // Logic Get Data Suspect and Correction
            $suspect = $this->reconDanaCheckSuspect($danaId, $date, 'date');
            $correction = $this->reconDanaCheckCorrection($danaId, $date, 'date');

            // Mapping Suspect and Correction
            $data[$i] = collect($data[$i]);
            $data[$i]->put('STATUS_SUSPECT', $suspect);
            $data[$i]->put('STATUS_CORRECTION', $correction);
        }

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->responseSummary(
            200,
            'Get List Recon Dana Success',
            $summary,
            $data
        );
    }

    public function filter(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'settled_dt' => ['required', 'string', 'max:10'],
                'group_biller' => ['required', 'string', 'max:50'],
                'type' => ['numeric', 'digits:1'],
                'status' => ['numeric', 'digits:1'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Data Variable
        $date = $request->settled_dt;
        $group = $request->group_biller;
        $type = (null == $request->type) ? null : $request->type;
        $status = (null == $request->status) ? null : $request->status;
        $items = (null == $request->items) ? 10 : $request->items;
        $total = null;

        // Check Data Group Biller
        $checkGroup = $this->groupBillerById($group);
        if (false == $checkGroup) :
            return $this->groupBillerNotFound();
        endif;

        // Get Data Summary
        try {
            $summary = $this->reconDanaSummary($group, $date, $type, $status);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Filter Data Recon Dana Failed', $th->getMessage());
            Log::error('Get Data Summary', $th->getMessage());
        }

        // Response Not Found
        if (false == $summary) :
            return $this->responseNotFound('Filter Data Recon Dana Not Found');
        endif;

        // Hitung Jumlah Data Summary
        $countSummary = count($summary);

        // Get Data Process and Unprocess Summary
        try {
            $nilai = $this->reconDanaProcessList($date);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Filter Data Recon Dana Failed', $th->getMessage());
            Log::error('Get Process and Unprocess', $th->getMessage());
        }

        // Get Process, Unprocess & Total
        for ($i=0; $i < $countSummary; $i++) {
            // Handle Value For Process Amount Transfer
            if ($nilai[$i]['STATUS'] == 1) :
                $process[] = $nilai[$i]['AMOUNT_TRANSFER'];
            else :
                $process[] = 0;
            endif;

            // Handle Value For Unrocess Amount Transfer
            if ($nilai[$i]['STATUS'] == 0 || $nilai[$i]['STATUS'] == 2 || $nilai[$i]['STATUS'] == 3) :
                $unprocess[] = $nilai[$i]['AMOUNT_TRANSFER'];
            else :
                $unprocess[] = 0;
            endif;

            // Get Data Total Summary
            $total[] = $process[$i] + $unprocess[$i];
        }

        // Get Data Total -> Mapping Data Summary
        for ($i=0; $i < $countSummary; $i++) {
            // Mapping data For Response Summary
            $summary[$i] = collect($summary[$i]);
            $summary[$i]->put('SUM_ACCOUNT', $summary[$i]['BANK'] .' - '. $summary[$i]['ACCOUNT_NUMBER']);
            $summary[$i]->put('SUM_PROCESS', $process[$i]);
            $summary[$i]->put('SUM_UNPROCESS', $unprocess[$i]);
            $summary[$i]->put('SUM_TOTAL', $total[$i]);
            $summary[$i]->forget('BANK');
            $summary[$i]->forget('ACCOUNT_NUMBER');
            $summary[$i]->forget('BILLER');
            $summary[$i]->forget('AMOUNT_TRANSFER');
        }

        // Logic Get Data Filter
        try {
            $data = $this->reconDanaList($group, $date, $items, $type, $status);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Filter Data Recon Dana Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $count = count($data);

        // Status Suspect & Correction
        for ($i=0; $i < $count; $i++) {
            $danaId = $data[$i]['ID'];

            // Logic Get Data Suspect and Correction
            $suspect = $this->reconDanaCheckSuspect($danaId, $date, 'date');
            $correction = $this->reconDanaCheckCorrection($danaId, $date, 'date');

            // Mapping Suspect and Correction
            $data[$i] = collect($data[$i]);
            $data[$i]->put('STATUS_SUSPECT', $suspect);
            $data[$i]->put('STATUS_CORRECTION', $correction);
        }

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->responseSummary(
            200,
            'Filter Data Recon Dana Success',
            $summary,
            $data
        );
    }

    public function listCorrection(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $items = (null == $request->items) ? 10 : $request->items;

        // Check Data Recon Dana
        $checkReconDana = $this->reconDanaById($id);
        if (false == $checkReconDana) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get data
        $data = $this->reconDanaCorrection($id, $items);

        // Response Correction Not Found
        if (false == $data) :
            return $this->responseNotFound('Data Correction Recon Dana Not Found');
        endif;

        // Mapping Correction Value
        $countData = count($data);
        for ($i=0; $i < $countData; $i++) {
            $value = $data[$i]->VALUE . $data[$i]->AMOUNT;

            $data[$i] = collect($data[$i]);
            $data[$i]->put('AMOUNT', $value);
            $data[$i]->forget('VALUE');
        }

        // Response Success
        if (null != $data) :
            // Add index number
            $data = $this->addIndexNumber($data);
            return $this->generalDataResponse(200, 'Get List Correction Recon Dana Success', $data);
        endif;

        // Response Error
        if (!$data) :
            return $this->failedResponse('Get List Correction Recon Dana Failed');
        endif;
    }

    public function listSuspect(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $items = (null == $request->items) ? 10 : $request->items;
        $tranMain = [];

        // Check Data Recon Id
        $checkReconDana = $this->reconDanaById($id);
        if (false == $checkReconDana) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get Data
        try {
            $data = $this->reconDanaSuspect($id, $items);
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Get List Suspect Recon Dana Failed', $th->getMessage());
        }

        // Return Suspect Not Found
        if (false == $data) :
            return $this->responseNotFound('Data Suspect Recon Dana Not Found');
        endif;

        // Hitung Jumlah Data
        $countData = count($data);

        // Logic Get Data Tran Main
        for ($i=0; $i < $countData; $i++) {
            // Inisialisasi Variable
            $table = $data[$i]['TABLE'];
            $columnSubid = $data[$i]['SUBID_COLUMN'];
            $columnRefnum = $data[$i]['REFNUM_COLUMN'];
            $subid = $data[$i]['SUBID'];
            $refnum = $data[$i]['SW_REFNUM'];

            // Logic Get Data
            $tranMain[$i] = DB::connection('server_recon')
            ->table($table)
            ->select('CSM_TM_NAME AS CUSTOMER_NAME')
            ->where($columnSubid, $subid)
            ->where($columnRefnum, $refnum)
            ->first();
        }

        // Mapping Data and Train main
        for ($i=0; $i < $countData; $i++) {
            // Handler Null Customer Name
            $customer = (null != $tranMain[$i]) ? $tranMain[$i]->CUSTOMER_NAME : null;

            // Logic Maping Data
            $data[$i] = collect($data[$i]);
            $data[$i]->put('CUSTOMER_NAME', $customer);
            $data[$i]->forget('PRODUCT');
            $data[$i]->forget('TABLE');
            $data[$i]->forget('SUBID');
            $data[$i]->forget('SUBID_COLUMN');
            $data[$i]->forget('REFNUM_COLUMN');
        }

        // Response Not Found
        if (null == $countData) :
            return $this->responseNotFound('Data Suspect Recon Dana Not Found');
        endif;

        // Response Success
        if ($data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(200, 'Get List Suspect Recon Dana Success', $data);
        endif;
    }

    public function byId(Request $request)
    {
        // Validation Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'interval_date' => ['required', 'array', 'min:1'],
                'interval_date.*' => ['string', 'date_format:Y-m-d'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $date = $request->interval_date;
        $items = (null == $request->items) ? 10 : $request->items;

        // Check Data Recon Dana
        $checkData = $this->reconDanaById($id);
        if (false == $checkData) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get Data
        try {
            $data = CoreReconData::select(
                'CSC_RDT_PRODUCT AS PRODUCT',
                DB::raw('SUM(CSC_RDT_NBILL) AS TRX_NBILL'),
                DB::raw('SUM(CSC_RDT_NMONTH) AS TRX_NMONTH'),
                DB::raw('SUM(CSC_RDT_FEE) AS TRX_FEE'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS TRX_FEE_ADMIN'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE + CSC_RDT_FEE_ADMIN) AS TRX_TOTAL_FEE'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                'FT.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
                'CSC_RDT_USER_SETTLED AS USER_SETTLED',
                'CSC_RDT_STATUS AS STATUS',
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSC_RDT_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->reconDana($id)
            ->whereBetween('CSC_RDT_TRX_DT', $date)
            ->groupBy('CSC_RDT_PRODUCT')
            ->groupBy('CSC_RDT_STATUS')
            ->orderBy('CSC_RDT_PRODUCT', 'ASC')
            ->orderBy('CSC_RDT_CID', 'ASC')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by ID Recon Dana Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $countData = count($data);

        // Response Data Not Found
        if (null == $countData) :
            return $this->responseNotFound('Data List by ID Recon Dana Not Found');
        endif;

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->generalDataResponse(200, 'Get List by ID Recon Dana Success', $data);
    }

    public function byIdSuspect(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'interval_date' => ['required', 'array', 'min:1', 'max:2'],
                'interval_date.*' => ['string', 'date_format:Y-m-d'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $danaId = $request->id;
        $dateRange = $request->interval_date;
        $items = (null == $request->items) ? 10 : $request->items;
        $paid_CLAIM = [];
        $canceled = [];

        // Check Data recon Dana
        $checkDana = $this->reconDanaById($danaId);

        // Response Recon Dana Not Found
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        /**
         * Logic Get Data Suspect
         * -> Dimaksudkan untuk mengetahui berapa jumlah data yang memiliki suspect
         */
        try {
            $data = $this->reconDanaByIdSuspect($danaId, $dateRange);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by ID Suspect Recon Dana Failed', $th->getMessage());
        }

        // Response Data Not Found
        if (false == $data) :
            return $this->responseNotFound('Data List by ID Suspect Recon Dana Not Found');
        endif;

        // Hitung Jumlah Data
        $count = count($data);

        // Logic Get Data Suspect
        for ($i=0; $i < $count; $i++) :
            $product = $data[$i]['PRODUCT'];
            $nullSuspect = [
                'PRODUCT' => $product,
                'NBILL' => 0,
                'NMONTH' => 0,
                'FEE' => 0,
                'FEE_ADMIN' => 0,
                'TOTAL_FEE' => 0,
                'FEE_BILLER' => 0,
                'FEE_BILLER_AMOUNT' => 0,
                'FEE_VSI' => 0,
                'FEE_VSI_AMOUNT' => 0,
                'BILLER_AMOUNT' => 0,
                'FORMULA_TRANSFER' => null,
                'CLAIM_VSI' => 0,
                'CLAIM_VSI_AMOUNT' => 0,
                'CLAIM_PARTNER' => 0,
                'CLAIM_PARTNER_AMOUNT' => 0,
            ];

            // Get Data Paid And Suspect
            for ($n=0; $n <= 1; $n++) :
                $status = $n;
                $suspect = $this->reconDanaByIdSuspect($danaId, $dateRange, $product, $status);

                // Logic Split Data Paid & Canceled
                if (0 == $status) :
                    $paid[] = (false == $suspect) ? $nullSuspect : $suspect[0];
                elseif (1 == $status) :
                    $canceled[] = (false == $suspect) ? $nullSuspect : $suspect[0];
                endif;
            endfor;
        endfor;

        // Logic Mapping Response data
        for ($d=0; $d < $count; $d++) :
            $product = $data[$d]['PRODUCT'];

            $response[] = [
                'PRODUCT' => $product,
                // Paid
                'PAID_NBILL' => $paid[$d]['NBILL'],
                'PAID_NMONTH' => $paid[$d]['NMONTH'],
                'PAID_FEE' => $paid[$d]['FEE'],
                'PAID_FEE_ADMIN' => $paid[$d]['FEE_ADMIN'],
                'PAID_TOTAL_FEE' => $paid[$d]['TOTAL_FEE'],
                'PAID_FEE_BILLER' => $paid[$d]['FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $paid[$d]['FEE_BILLER_AMOUNT'],
                'PAID_FEE_VSI' => $paid[$d]['FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $paid[$d]['FEE_VSI_AMOUNT'],
                'PAID_BILLER_AMOUNT' => $paid[$d]['BILLER_AMOUNT'],
                'PAID_FORMULA_TRANSFER' => $paid[$d]['FORMULA_TRANSFER'],
                'PAID_CLAIM_VSI' => $paid[$d]['CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $paid[$d]['CLAIM_VSI_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $paid[$d]['CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$d]['CLAIM_PARTNER_AMOUNT'],
                // Canceled
                'CANCELED_NBILL' => $canceled[$d]['NBILL'],
                'CANCELED_NMONTH' => $canceled[$d]['NMONTH'],
                'CANCELED_FEE' => $canceled[$d]['FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$d]['FEE_ADMIN'],
                'CANCELED_TOTAL_FEE' => $canceled[$d]['TOTAL_FEE'],
                'CANCELED_FEE_BILLER' => $canceled[$d]['FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$d]['FEE_BILLER_AMOUNT'],
                'CANCELED_FEE_VSI' => $canceled[$d]['FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $canceled[$d]['FEE_VSI_AMOUNT'],
                'CANCELED_BILLER_AMOUNT' => $canceled[$d]['BILLER_AMOUNT'],
                'CANCELED_FORMULA_TRANSFER' => $canceled[$d]['FORMULA_TRANSFER'],
                'CANCELED_CLAIM_VSI' => $canceled[$d]['CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$d]['CLAIM_VSI_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $canceled[$d]['CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$d]['CLAIM_PARTNER_AMOUNT'],
            ];
        endfor;

        // Add Index Number
        $response = $this->addIndexNumber($response);

        // Make Paginate
        $response = $this->createPaginate($response, $items);

        // Response Success
        return $this->generalDataResponse(200, 'Get List by ID Suspect Recon Dana Success', $response);
    }

    public function listSuspectProduct(Request $request)
    {
        // Validation Exception
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'product' => ['required', 'string', 'max:100'],
                'suspect_status' => ['required', 'numeric', 'digits:1'],
                'interval_date' => ['required', 'array', 'min:1', 'max:2'],
                'interval_date.*' => ['string', 'max:10'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $product = $request->product;
        $status = $request->suspect_status;
        $date = $request->interval_date;
        $items = (null != $request->items) ? $request->items : 10;

        // Check Data Recon Dana
        $checkDana = $this->reconDanaById($id);
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Check Data Product
        $checkProduct = $this->productCheckData($product);
        if (false == $checkProduct) :
            return $this->productNotFound();
        endif;

        // Logic Get Data
        try {
            $data = TrxCorrection::select(
                'CSM_TC_PRODUCT AS CUSTOMER_ID',
                'CSM_TC_PRODUCT AS CUSTOMER_NAME',
                'CSM_TC_CID AS CID',
                'CSM_TC_TRX_DT AS TRX_DATE',
                'CSM_TC_PROCESS_DT AS PROCESS_DATE',
                DB::raw('SUM(CSM_TC_NBILL) AS NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE + CSM_TC_FEE_ADMIN) AS TOTAL'),
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS BILLER_AMOUNT'),
                //
                'FH.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                //
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
                //
                'CSM_TC_SW_REFNUM AS SW_REFNUM',
                'CSM_TC_STATUS_TRX AS STATUS_TRX',
                'CSM_TC_STATUS_FUNDS AS STATUS_FUNDS',
                //
                'TD.CSC_TD_NAME AS PRODUCT',
                'TD.CSC_TD_TABLE AS TABLE',
                'CSM_TC_SUBID AS SUBID',
                'TD.CSC_TD_SUBID_COLUMN AS SUBID_COLUMN',
                'TD.CSC_TD_SWITCH_REFNUM_COLUMN AS REFNUM_COLUMN'
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FH',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FH.CSC_FH_ID'
            )
            ->join(
                'CSCCORE_TRANSACTION_DEFINITION AS TD',
                'CSM_TC_PRODUCT',
                '=',
                'TD.CSC_TD_NAME'
            )
            ->reconDana($id)
            ->product($product)
            ->status($status)
            ->dateRange($date)
            ->groupBy('CSM_TC_RECON_DANA_ID')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List Suspect Recon Dana Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $countData = count($data);

        // Logic Get Data Custommer
        for ($i=0; $i < $countData; $i++) {
            // Inisialisasi Variable
            $table = $data[$i]['TABLE'];
            $columnSubid = $data[$i]['SUBID_COLUMN'];
            $columnRefnum = $data[$i]['REFNUM_COLUMN'];
            $subid = $data[$i]['SUBID'];
            $refnum = $data[$i]['SW_REFNUM'];

            // Logic Get Data
            $tranMain[$i] = DB::connection('server_recon')
            ->table($table)
            ->select('CSM_TM_NAME AS CUSTOMER_NAME')
            ->where($columnSubid, $subid)
            ->where($columnRefnum, $refnum)
            ->first();
        }

        // Mapping Data
        for ($i=0; $i < $countData; $i++) {
            $customer = $tranMain[$i]->CUSTOMER_NAME;

            $data[$i] = collect($data[$i]);
            $data[$i]->put('CUSTOMER_NAME', $customer);
            $data[$i]->forget('PRODUCT');
            $data[$i]->forget('TABLE');
            $data[$i]->forget('SUBID');
            $data[$i]->forget('SUBID_COLUMN');
            $data[$i]->forget('REFNUM_COLUMN');
        }

        // Return Response Not Found
        if (null == $countData) :
            return $this->responseNotFound('Data Suspect by Product Recon Dana Not Found');
        endif;

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Return Success
        return $this->generalDataResponse(200, 'Get List Suspect by Product Recon Dana Success', $data);
    }

    public function byProduct(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'product' => ['required', 'string', 'max:100'],
                'interval_date' => ['required', 'array', 'min:1', 'max:2'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $product = $request->product;
        $date = $request->interval_date;
        $items = (null == $request->items) ? 10 : $request->items;
        $nullPaid = [
            'PAID_NBILL' => 0,
            'PAID_NMONTH' => 0,
            'PAID_FEE' => 0,
            'PAID_FEE_ADMIN' => 0,
            'PAID_FEE_ADMIN_AMOUNT' => 0,
            'PAID_TOTAL' => 0,
            'PAID_FEE_BILLER' => 0,
            'PAID_FEE_BILLER_AMOUNT' => 0,
            'PAID_FEE_VSI' => 0,
            'PAID_FEE_VSI_AMOUNT' => 0,
            'PAID_BILLER_AMOUNT' => 0,
            'PAID_FORMULA_TRANSFER' => 0,
            'PAID_CLAIM_VSI' => 0,
            'PAID_CLAIM_VSI_AMOUNT' => 0,
            'PAID_CLAIM_PARTNER' => 0,
            'PAID_CLAIM_PARTNER_AMOUNT' => 0,
        ];
        $nullCanceled = [
            'CANCELED_NBILL' => 0,
            'CANCELED_NMONTH' => 0,
            'CANCELED_FEE' => 0,
            'CANCELED_FEE_ADMIN' => 0,
            'CANCELED_FEE_ADMIN_AMOUNT' => 0,
            'CANCELED_TOTAL' => 0,
            'CANCELED_FEE_BILLER' => 0,
            'CANCELED_FEE_BILLER_AMOUNT' => 0,
            'CANCELED_FEE_VSI' => 0,
            'CANCELED_FEE_VSI_AMOUNT' => 0,
            'CANCELED_BILLER_AMOUNT' => 0,
            'CANCELED_FORMULA_TRANSFER' => 0,
            'CANCELED_CLAIM_VSI' => 0,
            'CANCELED_CLAIM_VSI_AMOUNT' => 0,
            'CANCELED_CLAIM_PARTNER' => 0,
            'CANCELED_CLAIM_PARTNER_AMOUNT' => 0,
        ];

        // Check Data Recon Dana
        $checkDana = $this->reconDanaById($id);
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Check Data Product
        $checkProduct = $this->productCheckData($product);
        if (false == $checkProduct) :
            return $this->productNotFound();
        endif;

        // Logic Get Data
        try {
            $data = CoreReconData::select(
                'CSC_RDT_PRODUCT AS PRODUCT',
                'CSC_RDT_CID AS CID',
                'DC.CSC_DC_NAME AS CID_NAME',
                DB::raw('SUM(CSC_RDT_NBILL) AS TRX_NBILL'),
                DB::raw('SUM(CSC_RDT_NMONTH) AS TRX_NMONTH'),
                DB::raw('SUM(CSC_RDT_FEE) AS TRX_FEE'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS TRX_FEE_ADMIN'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT + CSC_RDT_FEE) AS TRX_TOTAL'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                'FH.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FH',
                'CSC_RDT_FORMULA_TRANSFER',
                '=',
                'FH.CSC_FH_ID'
            )
            ->leftJoin(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDT_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->reconDana($id)
            ->product($product)
            ->dateRange($date)
            ->groupBy('CID')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->failedResponse('Get List by Product Recon Dana Failed');
        }

        // Hitung Jumlah Data
        $countData = count($data);

        // Return Response Not Found
        if (null == $countData) :
            return $this->responseNotFound('Data List by Product Recon Dana Not Found');
        endif;

        // Logic Get Data Suspect
        try {
            for ($i=0; $i < $countData; $i++) {
                $suspect[] = TrxCorrection::select(
                    'CSM_TC_CID AS CID',
                    'CSM_TC_STATUS_TRX AS STATUS_TRX',
                    DB::raw('SUM(CSM_TC_NBILL) AS CORR_NBILL'),
                    DB::raw('SUM(CSM_TC_NMONTH) AS CORR_NMONTH'),
                    DB::raw('SUM(CSM_TC_FEE) AS CORR_FEE'),
                    DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CORR_FEE_ADMIN'),
                    DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS CORR_FEE_ADMIN_AMOUNT'),
                    DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT + CSM_TC_FEE) AS CORR_TOTAL'),
                    DB::raw('SUM(CSM_TC_FEE_BILLER) AS CORR_FEE_BILLER'),
                    DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS CORR_FEE_BILLER_AMOUNT'),
                    DB::raw('SUM(CSM_TC_FEE_VSI) AS CORR_FEE_VSI'),
                    DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS CORR_FEE_VSI_AMOUNT'),
                    DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CORR_BILLER_AMOUNT'),
                    'FH.CSC_FH_FORMULA AS CORR_FORMULA_TRANSFER',
                    DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CORR_CLAIM_VSI'),
                    DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CORR_CLAIM_VSI_AMOUNT'),
                    DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CORR_CLAIM_PARTNER'),
                    DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CORR_CLAIM_PARTNER_AMOUNT'),
                )
                ->join(
                    'CSCCORE_FORMULA_TRANSFER AS FH',
                    'CSM_TC_FORMULA_TRANSFER',
                    '=',
                    'FH.CSC_FH_ID'
                )
                ->whereExists(function ($query) {
                    $query->from('CSCCORE_RECON_DATA AS DATA')
                    ->select(
                        'DATA.CSC_RDT_RECON_DANA_ID',
                        'DATA.CSC_RDT_PRODUCT',
                        'DATA.CSC_RDT_TRX_DT',
                    )
                    ->whereColumn('CSM_TC_RECON_DANA_ID', 'DATA.CSC_RDT_RECON_DANA_ID')
                    ->whereColumn('CSM_TC_PRODUCT', 'DATA.CSC_RDT_PRODUCT')
                    ->whereColumn('CSM_TC_TRX_DT', 'DATA.CSC_RDT_TRX_DT');
                })
                ->cid($data[$i]->CID)
                ->whereNull('CSM_TC_RECON_ID')
                ->reconDana($id)
                ->product($product)
                ->dateRange($date)
                ->groupBy('CID')
                ->groupBy('STATUS_TRX')
                ->get();
            }

            // Hitung Jumlah Data Suspect
            $countSuspect = count($suspect);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by Product Recon Dana Failed', $th->getMessage());
        }

        // Logic Grrouping Paid and Canceled
        for ($i=0; $i < $countData; $i++) {
            // Inisialisasi Paid and Canceled
            if (null == $countSuspect) :
                $paid[] = $nullPaid;
                $canceled[] = $nullCanceled;
            elseif (isset($suspect[$i][0])) :
                $countSuspect = count($suspect[$i]);
                for ($j=0; $j < $countSuspect; $j++) :
                    $dataSuspect = $suspect[$i][$j];
                    $status = $dataSuspect['STATUS_TRX'];
                    $nbill = $dataSuspect['CORR_NBILL'];
                    $nmonth = $dataSuspect['CORR_NMONTH'];
                    $fee = $dataSuspect['CORR_FEE'];
                    $feeAdmin = $dataSuspect['CORR_FEE_ADMIN'];
                    $feeAdminAmount = $dataSuspect['CORR_FEE_ADMIN_AMOUNT'];
                    $total = $dataSuspect['CORR_TOTAL'];
                    $feeBiller = $dataSuspect['CORR_FEE_BILLER'];
                    $feeBillerAmount = $dataSuspect['CORR_FEE_BILLER_AMOUNT'];
                    $feeVsi = $dataSuspect['CORR_FEE_VSI'];
                    $feeVsiAmount = $dataSuspect['CORR_FEE_VSI_AMOUNT'];
                    $billerAmount = $dataSuspect['CORR_BILLER_AMOUNT'];
                    $formulaTransfer = $dataSuspect['CORR_FORMULA_TRANSFER'];
                    $claimVsi = $dataSuspect['CORR_CLAIM_VSI'];
                    $claimVsiAmount = $dataSuspect['CORR_CLAIM_VSI_AMOUNT'];
                    $claimPartner = $dataSuspect['CORR_CLAIM_PARTNER'];
                    $claimPartnerAmount = $dataSuspect['CORR_CLAIM_PARTNER_AMOUNT'];

                    // Grouping Status Paid and Cancel
                    if (0 == $status) :
                        $paid[] = [
                            'PAID_NBILL' => $nbill,
                            'PAID_NMONTH' => $nmonth,
                            'PAID_FEE' => $fee,
                            'PAID_FEE_ADMIN' => $feeAdmin,
                            'PAID_FEE_ADMIN_AMOUNT' => $feeAdminAmount,
                            'PAID_TOTAL' => $total,
                            'PAID_FEE_BILLER' => $feeBiller,
                            'PAID_FEE_BILLER_AMOUNT' => $feeBillerAmount,
                            'PAID_FEE_VSI' => $feeVsi,
                            'PAID_FEE_VSI_AMOUNT' => $feeVsiAmount,
                            'PAID_BILLER_AMOUNT' => $billerAmount,
                            'PAID_FORMULA_TRANSFER' => $formulaTransfer,
                            'PAID_CLAIM_VSI' => $claimVsi,
                            'PAID_CLAIM_VSI_AMOUNT' => $claimVsiAmount,
                            'PAID_CLAIM_PARTNER' => $claimPartner,
                            'PAID_CLAIM_PARTNER_AMOUNT' => $claimPartnerAmount,
                        ];

                        $canceled[] = $nullCanceled;

                        // Handle Canceled Transaction
                    elseif (1 == $status) :
                        $canceled[] = [
                            'CANCELED_NBILL' => $nbill,
                            'CANCELED_NMONTH' => $nmonth,
                            'CANCELED_FEE' => $fee,
                            'CANCELED_FEE_ADMIN' => $feeAdmin,
                            'CANCELED_FEE_ADMIN_AMOUNT' => $feeAdminAmount,
                            'CANCELED_TOTAL' => $total,
                            'CANCELED_FEE_BILLER' => $feeBiller,
                            'CANCELED_FEE_BILLER_AMOUNT' => $feeBillerAmount,
                            'CANCELED_FEE_VSI' => $feeVsi,
                            'CANCELED_FEE_VSI_AMOUNT' => $feeVsiAmount,
                            'CANCELED_BILLER_AMOUNT' => $billerAmount,
                            'CANCELED_FORMULA_TRANSFER' => $formulaTransfer,
                            'CANCELED_CLAIM_VSI' => $claimVsi,
                            'CANCELED_CLAIM_VSI_AMOUNT' => $claimVsiAmount,
                            'CANCELED_CLAIM_PARTNER' => $claimPartner,
                            'CANCELED_CLAIM_PARTNER_AMOUNT' => $claimPartnerAmount,
                        ];

                        $paid[] = $nullPaid;
                    endif;
                endfor;
            else :
                $paid[$i] = $nullPaid;
                $canceled[$i] = $nullCanceled;
            endif;
        }

        // Logic Get Total & Mapping data
        for ($i=0; $i < $countData; $i++) {
            // Data Total Untuk Function Maping
            $field = [
                'TRX_NBILL' => $data[$i]['TRX_NBILL'],
                'TRX_NMONTH' => $data[$i]['TRX_NMONTH'],
                'TRX_FEE' => $data[$i]['TRX_FEE'],
                'TRX_FEE_ADMIN' => $data[$i]['TRX_FEE_ADMIN'],
                'TRX_FEE_ADMIN_AMOUNT' => $data[$i]['TRX_FEE_ADMIN_AMOUNT'],
                'TRX_FEE_VSI' => $data[$i]['TRX_FEE_VSI'],
                'TRX_FEE_VSI_AMOUNT' => $data[$i]['TRX_FEE_VSI_AMOUNT'],
                'TRX_TOTAL_FEE' => $data[$i]['TRX_TOTAL_FEE'],
                'TRX_FEE_BILLER' => $data[$i]['TRX_FEE_BILLER'],
                'TRX_FEE_BILLER_AMOUNT' => $data[$i]['TRX_FEE_BILLER_AMOUNT'],
                'TRX_CLAIM_VSI' => $data[$i]['TRX_CLAIM_VSI'],
                'TRX_CLAIM_VSI_AMOUNT' => $data[$i]['TRX_CLAIM_VSI_AMOUNT'],
                'TRX_BILLER' => $data[$i]['TRX_BILLER_AMOUNT'],
                'TRX_CLAIM_PARTNER' => $data[$i]['TRX_CLAIM_PARTNER'],
                'TRX_CLAIM_PARTNER_AMOUNT' => $data[$i]['TRX_CLAIM_PARTNER_AMOUNT'],

                'CANCELED_NBILL' => $canceled[$i]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $canceled[$i]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $canceled[$i]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$i]['CANCELED_FEE_ADMIN'],
                'CANCELED_FEE_ADMIN_AMOUNT' => $canceled[$i]['CANCELED_FEE_ADMIN_AMOUNT'],
                'CANCELED_FEE_VSI' => $canceled[$i]['CANCELED_FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $canceled[$i]['CANCELED_FEE_VSI_AMOUNT'],
                'CANCELED_TOTAL_FEE' => $canceled[$i]['CANCELED_TOTAL'],
                'CANCELED_FEE_BILLER' => $canceled[$i]['CANCELED_FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$i]['CANCELED_FEE_BILLER_AMOUNT'],
                'CANCELED_CLAIM_VSI' => $canceled[$i]['CANCELED_CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$i]['CANCELED_CLAIM_VSI_AMOUNT'],
                'CANCELED_BILLER' => $canceled[$i]['CANCELED_BILLER_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $canceled[$i]['CANCELED_CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$i]['CANCELED_CLAIM_PARTNER_AMOUNT'],

                'PAID_NBILL' => $paid[$i]['PAID_NBILL'],
                'PAID_NMONTH' => $paid[$i]['PAID_NMONTH'],
                'PAID_FEE' => $paid[$i]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $paid[$i]['PAID_FEE_ADMIN'],
                'PAID_FEE_ADMIN_AMOUNT' => $paid[$i]['PAID_FEE_ADMIN_AMOUNT'],
                'PAID_FEE_VSI' => $paid[$i]['PAID_FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $paid[$i]['PAID_FEE_VSI_AMOUNT'],
                'PAID_TOTAL_FEE' => $paid[$i]['PAID_TOTAL'],
                'PAID_FEE_BILLER' => $paid[$i]['PAID_FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $paid[$i]['PAID_FEE_BILLER_AMOUNT'],
                'PAID_CLAIM_VSI' => $paid[$i]['PAID_CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $paid[$i]['PAID_CLAIM_VSI_AMOUNT'],
                'PAID_BILLER' => $paid[$i]['PAID_BILLER_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $paid[$i]['PAID_CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$i]['PAID_CLAIM_PARTNER_AMOUNT'],
            ];

            // Inisialisasi Transaksi
            $tnbil = $field['TRX_NBILL'];
            $tnmonth = $field['TRX_NMONTH'];
            $tfee = $field['TRX_FEE'];
            $tFeeAdminAmount = $field['TRX_FEE_ADMIN_AMOUNT'];
            $tFeeVsiAmount = $field['TRX_FEE_VSI_AMOUNT'];
            $tTotalFee = $field['TRX_TOTAL_FEE'];
            $tFeeBillerAmount = $field['TRX_FEE_BILLER_AMOUNT'];
            $tClaimVsiAmount = $field['TRX_CLAIM_VSI_AMOUNT'];
            $tBiller = $field['TRX_BILLER'];
            $tClaimPartnerAmount = $field['TRX_CLAIM_PARTNER_AMOUNT'];

            // Inisialisasi Paid
            $pnbil = $field['PAID_NBILL'];
            $pnmonth = $field['PAID_NMONTH'];
            $pfee = $field['PAID_FEE'];
            $pFeeAdminAmount = $field['PAID_FEE_ADMIN_AMOUNT'];
            $pFeeVsiAmount = $field['PAID_FEE_VSI_AMOUNT'];
            $pTotalFee = $field['PAID_TOTAL_FEE'];
            $pFeeBillerAmount = $field['PAID_FEE_BILLER_AMOUNT'];
            $pClaimVsiAmount = $field['PAID_CLAIM_VSI_AMOUNT'];
            $pBiller = $field['PAID_BILLER'];
            $pClaimPartnerAmount = $field['PAID_CLAIM_PARTNER_AMOUNT'];

            // Inisialisasi Canceled
            $cnbil = $field['CANCELED_NBILL'];
            $cnmonth = $field['CANCELED_NMONTH'];
            $cfee = $field['CANCELED_FEE'];
            $cFeeAdminAmount = $field['CANCELED_FEE_ADMIN_AMOUNT'];
            $cFeeVsiAmount = $field['CANCELED_FEE_VSI_AMOUNT'];
            $cTotalFee = $field['CANCELED_TOTAL_FEE'];
            $cFeeBillerAmount = $field['CANCELED_FEE_BILLER_AMOUNT'];
            $cClaimVsiAmount = $field['CANCELED_CLAIM_VSI_AMOUNT'];
            $cBiller = $field['CANCELED_BILLER'];
            $cClaimPartnerAmount = $field['CANCELED_CLAIM_PARTNER_AMOUNT'];

            $total = [
                'TOTAL_NBILL' => $tnbil + $pnbil - $cnbil,
                'TOTAL_NMONTH' => $tnmonth + $pnmonth - $cnmonth,
                'TOTAL_FEE' => $tfee + $pfee - $cfee,
                'TOTAL_FEE_ADMIN_AMOUNT' => $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                'TOTAL_FEE_VSI_AMOUNT' => $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                'TOTAL_TOTAL_FEE' => $tTotalFee + $pTotalFee - $cTotalFee,
                'TOTAL_FEE_BILLER_AMOUNT' => $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                'TOTAL_CLAIM_VSI_AMOUNT' => $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                'TOTAL_BILLER_AMOUNT' => $tBiller + $pBiller - $cBiller,
                'TOTAL_CLAIM_PARTNER_AMOUNT' => $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,
            ];

            // Logic Mapping
            $data[$i] = collect($data[$i]);
            // $data[$i]->put('CID_NAME', $customer);
            $this->reconDanaMapping($data[$i], $paid[$i]);
            $this->reconDanaMapping($data[$i], $canceled[$i]);
            $this->reconDanaMapping($data[$i], $total);
        }

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Return response Success
        return $this->generalDataResponse(200, 'Get List by Product Recon Dana Success', $data);
    }

    public function listByCid(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'product' => ['required', 'string', 'max:100'],
                'cid' => ['required', 'string', 'max:7'],
                'interval_date' => ['required', 'array', 'min:1', 'max:2'],
                'interval_date.*' => ['string', 'max:10'],
                'status' => ['required', 'numeric', 'digits:1'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $product = $request->product;
        $cid = $request->cid;
        $date = $request->interval_date;
        $status = $request->status;
        $items = (null == $request->items) ? 10 : $request->items;
        $paid = [];
        $canceled = [];
        $nullPaid = [
            'PAID_NBILL' => 0,
            'PAID_NMONTH' => 0,
            'PAID_FEE' => 0,
            'PAID_FEE_ADMIN' => 0,
            'PAID_FEE_ADMIN_AMOUNT' => 0,
            'PAID_TOTAL' => 0,
            'PAID_FEE_BILLER' => 0,
            'PAID_FEE_BILLER_AMOUNT' => 0,
            'PAID_FEE_VSI' => 0,
            'PAID_FEE_VSI_AMOUNT' => 0,
            'PAID_BILLER_AMOUNT' => 0,
            'PAID_FORMULA_TRANSFER' => 0,
            'PAID_CLAIM_VSI' => 0,
            'PAID_CLAIM_VSI_AMOUNT' => 0,
            'PAID_CLAIM_PARTNER' => 0,
            'PAID_CLAIM_PARTNER_AMOUNT' => 0,
        ];
        $nullCanceled = [
            'CANCELED_NBILL' => 0,
            'CANCELED_NMONTH' => 0,
            'CANCELED_FEE' => 0,
            'CANCELED_FEE_ADMIN' => 0,
            'CANCELED_FEE_ADMIN_AMOUNT' => 0,
            'CANCELED_TOTAL' => 0,
            'CANCELED_FEE_BILLER' => 0,
            'CANCELED_FEE_BILLER_AMOUNT' => 0,
            'CANCELED_FEE_VSI' => 0,
            'CANCELED_FEE_VSI_AMOUNT' => 0,
            'CANCELED_BILLER_AMOUNT' => 0,
            'CANCELED_FORMULA_TRANSFER' => 0,
            'CANCELED_CLAIM_VSI' => 0,
            'CANCELED_CLAIM_VSI_AMOUNT' => 0,
            'CANCELED_CLAIM_PARTNER' => 0,
            'CANCELED_CLAIM_PARTNER_AMOUNT' => 0,
        ];

        // Cek Data Recon Dana
        $checkDana = $this->reconDanaById($id);
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Check Data Product
        $checkProduct = $this->productCheckData($product);
        if (false == $checkProduct) :
            return $this->productNotFound();
        endif;

        // Check Data CID
        $checkCid = $this->cidCheckData($cid);
        if (false == $checkCid) :
            return $this->cidNotFound();
        endif;

        // Logic Get Data
        try {
            $data = CoreReconData::select(
                'CSC_RDT_PRODUCT AS PRODUCT',
                'CSC_RDT_CID AS CID',
                'DC.CSC_DC_NAME AS CID_NAME',
                'CSC_RDT_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSC_RDT_NBILL) AS TRX_NBILL'),
                DB::raw('SUM(CSC_RDT_NMONTH) AS TRX_NMONTH'),
                DB::raw('SUM(CSC_RDT_FEE) AS TRX_FEE'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS TRX_FEE_ADMIN'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT + CSC_RDT_FEE) AS TRX_TOTAL'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                'FH.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FH',
                'CSC_RDT_FORMULA_TRANSFER',
                '=',
                'FH.CSC_FH_ID'
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDT_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->reconDana($id)
            ->product($product)
            ->cid($cid)
            ->dateRange($date)
            ->status($status)
            ->groupBy('TRX_DT')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by CID Recon Dana Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $count = count($data);

        // Return Response Not Found
        if (null == $count) :
            return $this->responseNotFound('Data List by CID Recon Dana Not Found');
        endif;

        // Logic Get Data Suspect
        try {
            $suspect = TrxCorrection::select(
                'CSM_TC_STATUS_TRX AS STATUS',
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS CORR_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CORR_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CORR_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CORR_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS CORR_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT + CSM_TC_FEE) AS CORR_TOTAL'),
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS CORR_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS CORR_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CORR_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS CORR_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CORR_BILLER_AMOUNT'),
                'FH.CSC_FH_FORMULA AS CORR_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CORR_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CORR_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CORR_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CORR_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FH',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FH.CSC_FH_ID'
            )
            ->whereExists(function ($query) {
                $query->from('CSCCORE_RECON_DATA AS DATA')
                ->select(
                    'DATA.CSC_RDT_RECON_DANA_ID',
                    'DATA.CSC_RDT_PRODUCT',
                    'DATA.CSC_RDT_TRX_DT',
                )
                ->whereColumn('CSM_TC_RECON_DANA_ID', 'DATA.CSC_RDT_RECON_DANA_ID')
                ->whereColumn('CSM_TC_PRODUCT', 'DATA.CSC_RDT_PRODUCT')
                ->whereColumn('CSM_TC_TRX_DT', 'DATA.CSC_RDT_TRX_DT');
            })
            ->whereNull('CSM_TC_RECON_ID')
            ->reconDana($id)
            ->product($product)
            ->cid($cid)
            ->dateRange($date)
            ->groupBy('CSM_TC_TRX_DT')
            ->groupBy('CSM_TC_STATUS_TRX')
            ->get();

            // Hitung Data Suspect
            $countSuspect = count($suspect);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by CID Recon Dana Failed', $th->getMessage());
        }

        // Logic Grrouping Paid and Canceled
        for ($i=0; $i < $count; $i++) {
            // Inisialisasi Paid and Canceled
            if (null == $countSuspect) :
                $paid[] = $nullPaid;
                $canceled[] = $nullCanceled;
            elseif (isset($suspect[$i])) :
                $status = $suspect[$i]['STATUS_TRX'];
                $nbill = $suspect[$i]['CORR_NBILL'];
                $nmonth = $suspect[$i]['CORR_NMONTH'];
                $fee = $suspect[$i]['CORR_FEE'];
                $feeAdmin = $suspect[$i]['CORR_FEE_ADMIN'];
                $feeAdminAmount = $suspect[$i]['CORR_FEE_ADMIN_AMOUNT'];
                $total = $suspect[$i]['CORR_TOTAL'];
                $feeBiller = $suspect[$i]['CORR_FEE_BILLER'];
                $feeBillerAmount = $suspect[$i]['CORR_FEE_BILLER_AMOUNT'];
                $feeVsi = $suspect[$i]['CORR_FEE_VSI'];
                $feeVsiAmount = $suspect[$i]['CORR_FEE_VSI_AMOUNT'];
                $billerAmount = $suspect[$i]['CORR_BILLER_AMOUNT'];
                $formulaTransfer = $suspect[$i]['CORR_FORMULA_TRANSFER'];
                $claimVsi = $suspect[$i]['CORR_CLAIM_VSI'];
                $claimVsiAmount = $suspect[$i]['CORR_CLAIM_VSI_AMOUNT'];
                $claimPartner = $suspect[$i]['CORR_CLAIM_PARTNER'];
                $claimPartnerAmount = $suspect[$i]['CORR_CLAIM_PARTNER_AMOUNT'];

                // Grouping Status Paid and Cancel
                if (0 == $status) :
                    $paid[] = [
                        'PAID_NBILL' => $nbill,
                        'PAID_NMONTH' => $nmonth,
                        'PAID_FEE' => $fee,
                        'PAID_FEE_ADMIN' => $feeAdmin,
                        'PAID_FEE_ADMIN_AMOUNT' => $feeAdminAmount,
                        'PAID_TOTAL' => $total,
                        'PAID_FEE_BILLER' => $feeBiller,
                        'PAID_FEE_BILLER_AMOUNT' => $feeBillerAmount,
                        'PAID_FEE_VSI' => $feeVsi,
                        'PAID_FEE_VSI_AMOUNT' => $feeVsiAmount,
                        'PAID_BILLER_AMOUNT' => $billerAmount,
                        'PAID_FORMULA_TRANSFER' => $formulaTransfer,
                        'PAID_CLAIM_VSI' => $claimVsi,
                        'PAID_CLAIM_VSI_AMOUNT' => $claimVsiAmount,
                        'PAID_CLAIM_PARTNER' => $claimPartner,
                        'PAID_CLAIM_PARTNER_AMOUNT' => $claimPartnerAmount,
                    ];

                    $canceled[] = $nullCanceled;

                    // Handle Canceled Transaction
                elseif (1 == $status) :
                    $canceled[] = [
                        'CANCELED_NBILL' => $nbill,
                        'CANCELED_NMONTH' => $nmonth,
                        'CANCELED_FEE' => $fee,
                        'CANCELED_FEE_ADMIN' => $feeAdmin,
                        'CANCELED_FEE_ADMIN_AMOUNT' => $feeAdminAmount,
                        'CANCELED_TOTAL' => $total,
                        'CANCELED_FEE_BILLER' => $feeBiller,
                        'CANCELED_FEE_BILLER_AMOUNT' => $feeBillerAmount,
                        'CANCELED_FEE_VSI' => $feeVsi,
                        'CANCELED_FEE_VSI_AMOUNT' => $feeVsiAmount,
                        'CANCELED_BILLER_AMOUNT' => $billerAmount,
                        'CANCELED_FORMULA_TRANSFER' => $formulaTransfer,
                        'CANCELED_CLAIM_VSI' => $claimVsi,
                        'CANCELED_CLAIM_VSI_AMOUNT' => $claimVsiAmount,
                        'CANCELED_CLAIM_PARTNER' => $claimPartner,
                        'CANCELED_CLAIM_PARTNER_AMOUNT' => $claimPartnerAmount,
                    ];

                    $paid[] = $nullPaid;
                endif;
            else :
                $paid[$i] = $nullPaid;
                $canceled[$i] = $nullCanceled;
            endif;
        }

        // Logic Get Total & Mapping data
        for ($i=0; $i < $count; $i++) {
            // Data Total Untuk Function Maping
            $field = [
                'TRX_NBILL' => $data[$i]['TRX_NBILL'],
                'TRX_NMONTH' => $data[$i]['TRX_NMONTH'],
                'TRX_FEE' => $data[$i]['TRX_FEE'],
                'TRX_FEE_ADMIN' => $data[$i]['TRX_FEE_ADMIN'],
                'TRX_FEE_ADMIN_AMOUNT' => $data[$i]['TRX_FEE_ADMIN_AMOUNT'],
                'TRX_FEE_VSI' => $data[$i]['TRX_FEE_VSI'],
                'TRX_FEE_VSI_AMOUNT' => $data[$i]['TRX_FEE_VSI_AMOUNT'],
                'TRX_TOTAL_FEE' => $data[$i]['TRX_TOTAL_FEE'],
                'TRX_FEE_BILLER' => $data[$i]['TRX_FEE_BILLER'],
                'TRX_FEE_BILLER_AMOUNT' => $data[$i]['TRX_FEE_BILLER_AMOUNT'],
                'TRX_CLAIM_VSI' => $data[$i]['TRX_CLAIM_VSI'],
                'TRX_CLAIM_VSI_AMOUNT' => $data[$i]['TRX_CLAIM_VSI_AMOUNT'],
                'TRX_BILLER' => $data[$i]['TRX_BILLER_AMOUNT'],
                'TRX_CLAIM_PARTNER' => $data[$i]['TRX_CLAIM_PARTNER'],
                'TRX_CLAIM_PARTNER_AMOUNT' => $data[$i]['TRX_CLAIM_PARTNER_AMOUNT'],

                'CANCELED_NBILL' => $canceled[$i]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $canceled[$i]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $canceled[$i]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$i]['CANCELED_FEE_ADMIN'],
                'CANCELED_FEE_ADMIN_AMOUNT' => $canceled[$i]['CANCELED_FEE_ADMIN_AMOUNT'],
                'CANCELED_FEE_VSI' => $canceled[$i]['CANCELED_FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $canceled[$i]['CANCELED_FEE_VSI_AMOUNT'],
                'CANCELED_TOTAL_FEE' => $canceled[$i]['CANCELED_TOTAL'],
                'CANCELED_FEE_BILLER' => $canceled[$i]['CANCELED_FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$i]['CANCELED_FEE_BILLER_AMOUNT'],
                'CANCELED_CLAIM_VSI' => $canceled[$i]['CANCELED_CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$i]['CANCELED_CLAIM_VSI_AMOUNT'],
                'CANCELED_BILLER' => $canceled[$i]['CANCELED_BILLER_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $canceled[$i]['CANCELED_CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$i]['CANCELED_CLAIM_PARTNER_AMOUNT'],

                'PAID_NBILL' => $paid[$i]['PAID_NBILL'],
                'PAID_NMONTH' => $paid[$i]['PAID_NMONTH'],
                'PAID_FEE' => $paid[$i]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $paid[$i]['PAID_FEE_ADMIN'],
                'PAID_FEE_ADMIN_AMOUNT' => $paid[$i]['PAID_FEE_ADMIN_AMOUNT'],
                'PAID_FEE_VSI' => $paid[$i]['PAID_FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $paid[$i]['PAID_FEE_VSI_AMOUNT'],
                'PAID_TOTAL_FEE' => $paid[$i]['PAID_TOTAL'],
                'PAID_FEE_BILLER' => $paid[$i]['PAID_FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $paid[$i]['PAID_FEE_BILLER_AMOUNT'],
                'PAID_CLAIM_VSI' => $paid[$i]['PAID_CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $paid[$i]['PAID_CLAIM_VSI_AMOUNT'],
                'PAID_BILLER' => $paid[$i]['PAID_BILLER_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $paid[$i]['PAID_CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$i]['PAID_CLAIM_PARTNER_AMOUNT'],
            ];
            // Inisialisasi Transaksi
            $tnbil = $field['TRX_NBILL'];
            $tnmonth = $field['TRX_NMONTH'];
            $tfee = $field['TRX_FEE'];
            $tFeeAdminAmount = $field['TRX_FEE_ADMIN_AMOUNT'];
            $tFeeVsiAmount = $field['TRX_FEE_VSI_AMOUNT'];
            $tTotalFee = $field['TRX_TOTAL_FEE'];
            $tFeeBillerAmount = $field['TRX_FEE_BILLER_AMOUNT'];
            $tClaimVsiAmount = $field['TRX_CLAIM_VSI_AMOUNT'];
            $tBiller = $field['TRX_BILLER'];
            $tClaimPartnerAmount = $field['TRX_CLAIM_PARTNER_AMOUNT'];

            // Inisialisasi Paid
            $pnbil = $field['PAID_NBILL'];
            $pnmonth = $field['PAID_NMONTH'];
            $pfee = $field['PAID_FEE'];
            $pFeeAdminAmount = $field['PAID_FEE_ADMIN_AMOUNT'];
            $pFeeVsiAmount = $field['PAID_FEE_VSI_AMOUNT'];
            $pTotalFee = $field['PAID_TOTAL_FEE'];
            $pFeeBillerAmount = $field['PAID_FEE_BILLER_AMOUNT'];
            $pClaimVsiAmount = $field['PAID_CLAIM_VSI_AMOUNT'];
            $pBiller = $field['PAID_BILLER'];
            $pClaimPartnerAmount = $field['PAID_CLAIM_PARTNER_AMOUNT'];

            // Inisialisasi Canceled
            $cnbil = $field['CANCELED_NBILL'];
            $cnmonth = $field['CANCELED_NMONTH'];
            $cfee = $field['CANCELED_FEE'];
            $cFeeAdminAmount = $field['CANCELED_FEE_ADMIN_AMOUNT'];
            $cFeeVsiAmount = $field['CANCELED_FEE_VSI_AMOUNT'];
            $cTotalFee = $field['CANCELED_TOTAL_FEE'];
            $cFeeBillerAmount = $field['CANCELED_FEE_BILLER_AMOUNT'];
            $cClaimVsiAmount = $field['CANCELED_CLAIM_VSI_AMOUNT'];
            $cBiller = $field['CANCELED_BILLER'];
            $cClaimPartnerAmount = $field['CANCELED_CLAIM_PARTNER_AMOUNT'];

            $total = [
                'TOTAL_NBILL' => $tnbil + $pnbil - $cnbil,
                'TOTAL_NMONTH' => $tnmonth + $pnmonth - $cnmonth,
                'TOTAL_FEE' => $tfee + $pfee - $cfee,
                'TOTAL_FEE_ADMIN_AMOUNT' => $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                'TOTAL_FEE_VSI_AMOUNT' => $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                'TOTAL_TOTAL_FEE' => $tTotalFee + $pTotalFee - $cTotalFee,
                'TOTAL_FEE_BILLER_AMOUNT' => $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                'TOTAL_CLAIM_VSI_AMOUNT' => $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                'TOTAL_BILLER_AMOUNT' => $tBiller + $pBiller - $cBiller,
                'TOTAL_CLAIM_PARTNER_AMOUNT' => $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,
            ];

            // Logic Mapping
            $data[$i] = collect($data[$i]);
            // $data[$i]->put('CID_NAME', $customer);
            $this->reconDanaMapping($data[$i], $paid[$i]);
            $this->reconDanaMapping($data[$i], $canceled[$i]);
            $this->reconDanaMapping($data[$i], $total);
        }

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Resturn Response Success
        return $this->generalDataResponse(200, 'Get List by CID Recon Dana Success', $data);
    }

    public function history(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'product' => ['required', 'string', 'max:100'],
                'cid' => ['required', 'string', 'max:7'],
                'trx_date' => ['required', 'date_format:Y-m-d'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $product = $request->product;
        $cid = $request->cid;
        $date = $request->trx_date;
        $items = (null == $request->items) ? 10 : $request->items;

        // Check Data Recon Id
        $checkDana = $this->reconDanaById($id);
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Check Data Product
        $checkProduct = $this->productCheckData($product);
        if (false == $checkProduct) :
            return $this->productNotFound();
        endif;

        // Check Data CID
        $checkCid = $this->cidCheckData($cid);
        if (false == $checkCid) :
            return $this->cidNotFound();
        endif;

        // Check Data Latest
        $checkLatest = CoreReconData::select(
            'CSC_RDT_CID AS CID',
        )
        ->reconDana($id)
        ->product($product)
        ->cid($cid)
        ->date($date)
        ->first();

        // Response Not Found
        if (null == $checkLatest) :
            return $this->responseNotFound('Data List History Recon Dana Not Found');
        endif;

        // Get Data Latest
        try {
            $latest = CoreReconData::select(
                'CSC_RDT_CID AS VERSION',
                'CSC_RDT_PRODUCT AS PRODUCT',
                'CSC_RDT_CID AS CID',
                'DC.CSC_DC_NAME AS CID_NAME',
                'CSC_RDT_TRX_DT AS TRX_DT',
                'CSC_RDT_SETTLED_DT AS SETTLED_DT',
                DB::raw('SUM(CSC_RDT_NBILL) AS TRX_NBILL'),
                DB::raw('SUM(CSC_RDT_NMONTH) AS TRX_NMONTH'),
                DB::raw('SUM(CSC_RDT_FEE) AS TRX_FEE'),
                DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS TRX_FEE_ADMIN'),
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                'FH.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_FEE+CSC_RDT_FEE_ADMIN) AS TRX_TOTAL_FEE'),
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDT_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->joinFormulaTransfer()
            ->reconDana($id)
            ->product($product)
            // ->status($status)
            ->cid($cid)
            ->date($date)
            ->get();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List History Recon Dana Failed', $th->getMessage());
        }

        // Hitung Data Latest
        $countLatest = count($latest);

        // *** Logic Get Data Paid And Canceled Latest ***
        for ($i=0; $i < $countLatest; $i++) {
            // Logic Get Data Paid
            $latestPaid[$i] = TrxCorrection::select(
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS PAID_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS PAID_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS PAID_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS PAID_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN+CSM_TC_FEE) AS PAID_TOTAL_FEE'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS PAID_FEE_VSI'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS PAID_BILLER_AMOUNT'),
                'FH.CSC_FH_FORMULA AS PAID_FORMULA_TRANSFER'
            )
            ->joinFormulaTransfer()
            ->product($product)
            ->cid($cid)
            ->date($date)
            ->status(0)
            ->get();

            // Handler Null Paid Nmonth
            if (null == $latestPaid[$i][0]->PAID_NBILL) :
                $latestPaid[$i][0]->PAID_NBILL = 0;
            endif;

            // Handler Null Paid Nmonth
            if (null == $latestPaid[$i][0]->PAID_NMONTH) :
                $latestPaid[$i][0]->PAID_NMONTH = 0;
            endif;

            // Handler Null Paid Fee
            if (null == $latestPaid[$i][0]->PAID_FEE) :
                $latestPaid[$i][0]->PAID_FEE = 0;
            endif;

            // Handler Null Paid Fee Admin
            if (null == $latestPaid[$i][0]->PAID_FEE_ADMIN) :
                $latestPaid[$i][0]->PAID_FEE_ADMIN = 0;
            endif;

            // Handler Null Paid Total Fee
            if (null == $latestPaid[$i][0]->PAID_TOTAL_FEE) :
                $latestPaid[$i][0]->PAID_TOTAL_FEE = 0;
            endif;

            // Handler Null Paid Fee Vsi
            if (null == $latestPaid[$i][0]->PAID_FEE_VSI) :
                $latestPaid[$i][0]->PAID_FEE_VSI = 0;
            endif;

            // Handler Null Paid Biller Amount
            if (null == $latestPaid[$i][0]->PAID_BILLER_AMOUNT) :
                $latestPaid[$i][0]->PAID_BILLER_AMOUNT = 0;
            endif;

            // Logic Get Data Canceled
            $latestCanceled[$i] = TrxCorrection::select(
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS CANCELED_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CANCELED_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CANCELED_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CANCELED_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN+CSM_TC_FEE) AS CANCELED_TOTAL_FEE'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CANCELED_FEE_VSI'),
                'FH.CSC_FH_FORMULA AS CANCELED_FORMULA_TRANSFER'
            )
            ->joinFormulaTransfer()
            ->reconDana($id)
            ->product($product)
            ->cid($cid)
            ->date($date)
            ->status(1)
            ->get();

            // Handler Null Paid Nmonth
            if (null == $latestCanceled[$i][0]->CANCELED_NBILL) :
                $latestCanceled[$i][0]->CANCELED_NBILL = 0;
            endif;

            // Handler Null CANCELED Nmonth
            if (null == $latestCanceled[$i][0]->CANCELED_NMONTH) :
                $latestCanceled[$i][0]->CANCELED_NMONTH = 0;
            endif;

            // Handler Null CANCELED Fee
            if (null == $latestCanceled[$i][0]->CANCELED_FEE) :
                $latestCanceled[$i][0]->CANCELED_FEE = 0;
            endif;

            // Handler Null CANCELED Fee Admin
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_ADMIN) :
                $latestCanceled[$i][0]->CANCELED_FEE_ADMIN = 0;
            endif;

            // Handler Null CANCELED Total Fee
            if (null == $latestCanceled[$i][0]->CANCELED_TOTAL_FEE) :
                $latestCanceled[$i][0]->CANCELED_TOTAL_FEE = 0;
            endif;

            // Handler Null CANCELED Fee Vsi
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_VSI) :
                $latestCanceled[$i][0]->CANCELED_FEE_VSI = 0;
            endif;

            // Handler Null CANCELED Biller Amount
            if (null == $latestCanceled[$i][0]->CANCELED_BILLER_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_BILLER_AMOUNT = 0;
            endif;
        }
        // *** END OF Logic Get Data Paid And Canceled Latest ***

        // *** Logic Mapping Latest Data Field ***
        for ($i=0; $i < $countLatest; $i++) {
            $latest[$i] = collect($latest[$i]);

            // Data Paid Untuk Function Maping
            $dataLatestPaid = [
                'nama_field' => [
                    'PAID_NBILL',
                    'PAID_NMONTH',
                    'PAID_FEE',
                    'PAID_FEE_ADMIN',
                    'PAID_TOTAL_FEE',
                    'PAID_FEE_VSI',
                    'PAID_BILLER_AMOUNT',
                ],

                'value_field' => [
                    $latestPaid[$i][0]['PAID_NBILL'],
                    $latestPaid[$i][0]['PAID_NMONTH'],
                    $latestPaid[$i][0]['PAID_FEE'],
                    $latestPaid[$i][0]['PAID_FEE_ADMIN'],
                    $latestPaid[$i][0]['PAID_TOTAL_FEE'],
                    $latestPaid[$i][0]['PAID_FEE_VSI'],
                    $latestPaid[$i][0]['PAID_BILLER_AMOUNT'],
                ],

                'jumlah_field' => 7,
            ];

            // Data Canceled Untuk Function Maping
            $dataLatestCanceled = [
                'nama_field' => [
                    'CANCELED_NBILL',
                    'CANCELED_NMONTH',
                    'CANCELED_FEE',
                    'CANCELED_FEE_ADMIN',
                    'CANCELED_TOTAL_FEE',
                    'CANCELED_FEE_VSI',
                    'CANCELED_BILLER_AMOUNT',
                ],

                'value_field' => [
                    $latestCanceled[$i][0]['CANCELED_NBILL'],
                    $latestCanceled[$i][0]['CANCELED_NMONTH'],
                    $latestCanceled[$i][0]['CANCELED_FEE'],
                    $latestCanceled[$i][0]['CANCELED_FEE_ADMIN'],
                    $latestCanceled[$i][0]['CANCELED_TOTAL_FEE'],
                    $latestCanceled[$i][0]['CANCELED_FEE_VSI'],
                    $latestCanceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                ],

                'jumlah_field' => 7,
            ];

            // Data Total Untuk Function Maping
            $field[$i] = [
                'TRX_NBILL' => $latest[$i]['TRX_NBILL'],
                'TRX_NMONTH' => $latest[$i]['TRX_NMONTH'],
                'TRX_FEE' => $latest[$i]['TRX_FEE'],
                'TRX_FEE_ADMIN' => $latest[$i]['TRX_FEE_ADMIN'],
                'TRX_TOTAL_FEE' => $latest[$i]['TRX_TOTAL_FEE'],
                'TRX_FEE_VSI' => $latest[$i]['TRX_FEE_VSI'],
                'TRX_BILLER' => $latest[$i]['TRX_BILLER_AMOUNT'],

                'CANCELED_NBILL' => $latestCanceled[$i][0]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $latestCanceled[$i][0]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $latestCanceled[$i][0]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $latestCanceled[$i][0]['CANCELED_FEE_ADMIN'],
                'CANCELED_TOTAL_FEE' => $latestCanceled[$i][0]['CANCELED_TOTAL_FEE'],
                'CANCELED_FEE_VSI' => $latestCanceled[$i][0]['CANCELED_FEE_VSI'],
                'CANCELED_BILLER' => $latestCanceled[$i][0]['CANCELED_BILLER_AMOUNT'],

                'PAID_NBILL' => $latestPaid[$i][0]['PAID_NBILL'],
                'PAID_NMONTH' => $latestPaid[$i][0]['PAID_NMONTH'],
                'PAID_FEE' => $latestPaid[$i][0]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $latestPaid[$i][0]['PAID_FEE_ADMIN'],
                'PAID_TOTAL_FEE' => $latestPaid[$i][0]['PAID_TOTAL_FEE'],
                'PAID_FEE_VSI' => $latestPaid[$i][0]['PAID_FEE_VSI'],
                'PAID_BILLER' => $latestPaid[$i][0]['PAID_BILLER_AMOUNT'],
            ];

            $dataTotal = [
                'nama_field' => [
                    'TOTAL_NBILL',
                    'TOTAL_NMONTH',
                    'TOTAL_FEE',
                    'TOTAL_FEE_ADMIN',
                    'TOTAL_TOTAL_FEE',
                    'TOTAL_FEE_VSI',
                    'TOTAL_BILLER_AMOUNT',
                ],
                'value_field' => [
                    $field[$i]['TRX_NBILL'] + $field[$i]['PAID_NBILL'] - $field[$i]['CANCELED_NBILL'],
                    $field[$i]['TRX_NMONTH'] + $field[$i]['PAID_NMONTH'] - $field[$i]['CANCELED_NMONTH'],
                    $field[$i]['TRX_FEE'] + $field[$i]['PAID_FEE'] - $field[$i]['CANCELED_FEE'],
                    $field[$i]['TRX_FEE_ADMIN'] + $field[$i]['PAID_FEE_ADMIN'] - $field[$i]['CANCELED_FEE_ADMIN'],
                    $field[$i]['TRX_TOTAL_FEE'] + $field[$i]['PAID_TOTAL_FEE'] - $field[$i]['CANCELED_TOTAL_FEE'],
                    $field[$i]['TRX_FEE_VSI'] + $field[$i]['PAID_FEE_VSI'] - $field[$i]['CANCELED_FEE_VSI'],
                    $field[$i]['TRX_BILLER'] + $field[$i]['PAID_BILLER'] - $field[$i]['CANCELED_BILLER'],
                ],
                'jumlah_field' => 7,
            ];

            // $this->mappingByProduct($latest[$i], $dataTrx);
            $this->mappingByProduct($latest[$i], $dataLatestPaid);
            $this->mappingByProduct($latest[$i], $dataLatestCanceled);

            // Mapping Field Total
            $this->mappingByProduct($latest[$i], $dataTotal);

            // Mapping Version
            $latest[$i]->put('VERSION', 'Latest');

            // Menghilangkan waktu di settled dt
            $latest[$i]->put('SETTLED_DT', substr($latest[0]['SETTLED_DT'], 0, 10));
        }
        // *** End Of Logic Mapping Latest Data Field ***

        // Logic Get Data History
        try {
            $data = CoreReconDataHistory::select(
                'CSC_RDTH_VERSION AS VERSION',
                'CSC_RDTH_PRODUCT AS PRODUCT',
                'CSC_RDTH_CID AS CID',
                'DC.CSC_DC_NAME AS CID_NAME',
                'CSC_RDTH_TRX_DT AS TRX_DT',
                'CSC_RDTH_SETTLED_DT AS SETTLED_DT',
                DB::raw('SUM(CSC_RDTH_NBILL) AS TRX_NBILL'),
                DB::raw('SUM(CSC_RDTH_NMONTH) AS TRX_NMONTH'),
                DB::raw('SUM(CSC_RDTH_FEE) AS TRX_FEE'),
                DB::raw('SUM(CSC_RDTH_FEE_ADMIN) AS TRX_FEE_ADMIN'),
                DB::raw('SUM(CSC_RDTH_FEE+CSC_RDTH_FEE_ADMIN) AS TRX_TOTAL_FEE'),
                DB::raw('SUM(CSC_RDTH_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDTH_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                //
                'CSC_RDTH_RECON_ID AS RECON_ID'
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDTH_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->product($product)
            ->cid($cid)
            ->date($date)
            ->groupBy('VERSION')
            ->orderBy('VERSION', 'DESC')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List History Recon Dana Failed', $th->getMessage());
        }

        // Hitung jumlah History
        $countHistory = count($data);

        // Response History Not Found
        if (null == $countHistory) :
            // Add Index Number
            $latest = $this->addIndexNumber($latest);

            // Create Paginate
            $latest = $this->createPaginate($latest, $items);

            // Response Success
            return $this->generalDataResponse(200, 'Get List History Recon Dana Success', $latest);
        endif;

        // *** Logic Get Data Latest TRX, Paid, Canceled ***
        for ($i=0; $i < $countHistory; $i++) {
            // Get Recon Id dari Data History
            $reconId[] = $data[$i]->RECON_ID;
            $historyCid[] = $data[$i]->CID;
            $historyProduct[] = $data[$i]->PRODUCT;

            // Menghitung Status Paid/Dilunaskan
            $cekPaid[] = TrxCorrection::select(
                'HISTORY.CSC_RDTH_RECON_ID AS RECON_ID',
                'HISTORY.CSC_RDTH_PRODUCT AS PRODUCT',
                'HISTORY.CSC_RDTH_CID AS CID',
                'HISTORY.CSC_RDTH_TRX_DT AS TRX_DT',
                'HISTORY.CSC_RDTH_VERSION AS VERSION',
                DB::raw('SUM(CSM_TC_NBILL) AS PAID_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS PAID_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS PAID_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS PAID_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN) AS PAID_TOTAL_FEE'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS PAID_FEE_VSI'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS PAID_BILLER_AMOUNT'),
            )
            ->join(
                'CSCCORE_RECON_DATA_HISTORY AS HISTORY',
                'HISTORY.CSC_RDTH_RECON_ID',
                '=',
                'CSM_TC_RECON_ID'
            )
            ->recon($reconId[$i])
            ->status(0)
            ->orderBy('HISTORY.CSC_RDTH_VERSION', 'DESC')
            ->get();

            // Handler Null Paid Nbill
            if (null == $cekPaid[$i][0]->PAID_NBILL) :
                $cekPaid[$i][0]->PAID_NBILL = 0;
            endif;

            // Handler Null Paid Nmonth
            if (null == $cekPaid[$i][0]->PAID_NMONTH) :
                $cekPaid[$i][0]->PAID_NMONTH = 0;
            endif;

            // Handler Null Paid Fee
            if (null == $cekPaid[$i][0]->PAID_FEE) :
                $cekPaid[$i][0]->PAID_FEE = 0;
            endif;

            // Handler Null Paid Fee Admin
            if (null == $cekPaid[$i][0]->PAID_FEE_ADMIN) :
                $cekPaid[$i][0]->PAID_FEE_ADMIN = 0;
            endif;

            // Handler Null Paid Total Fee
            if (null == $cekPaid[$i][0]->PAID_TOTAL_FEE) :
                $cekPaid[$i][0]->PAID_TOTAL_FEE = 0;
            endif;

            // Handler Null Paid Fee Vsi
            if (null == $cekPaid[$i][0]->PAID_FEE_VSI) :
                $cekPaid[$i][0]->PAID_FEE_VSI = 0;
            endif;

            // Handler Null Paid Biller Amount
            if (null == $cekPaid[$i][0]->PAID_BILLER_AMOUNT) :
                $cekPaid[$i][0]->PAID_BILLER_AMOUNT = 0;
            endif;

            // Save To Variable Paid
            $paid[$i][0] = $cekPaid[$i][0];

            // Menghitung Status Canceled/Dibatalkan
            $cekCancel[] = TrxCorrection::select(
                'HISTORY.CSC_RDTH_RECON_ID AS RECON_ID',
                'HISTORY.CSC_RDTH_PRODUCT AS PRODUCT',
                'HISTORY.CSC_RDTH_CID AS CID',
                'HISTORY.CSC_RDTH_TRX_DT AS TRX_DT',
                'HISTORY.CSC_RDTH_VERSION AS VERSION',
                DB::raw('SUM(CSM_TC_NBILL) AS CANCELED_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CANCELED_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CANCELED_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CANCELED_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN) AS CANCELED_TOTAL_FEE'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CANCELED_FEE_VSI'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CANCELED_BILLER_AMOUNT'),
            )
            ->join(
                'CSCCORE_RECON_DATA_HISTORY AS HISTORY',
                'HISTORY.CSC_RDTH_RECON_ID',
                '=',
                'CSM_TC_RECON_ID'
            )
            ->recon($reconId[$i])
            ->status(1)
            ->orderBy('HISTORY.CSC_RDTH_VERSION', 'DESC')
            ->get();

            // Handler Null Canceled Nmonth
            if (null == $cekCancel[$i][0]->CANCELED_NBILL) :
                $cekCancel[$i][0]->CANCELED_NBILL = 0;
            endif;

            // Handler Null Canceled Nmonth
            if (null == $cekCancel[$i][0]->CANCELED_NMONTH) :
                $cekCancel[$i][0]->CANCELED_NMONTH = 0;
            endif;

            // Handler Null Canceled Fee
            if (null == $cekCancel[$i][0]->CANCELED_FEE) :
                $cekCancel[$i][0]->CANCELED_FEE = 0;
            endif;

            // Handler Null Canceled Fee Admin
            if (null == $cekCancel[$i][0]->CANCELED_FEE_ADMIN) :
                $cekCancel[$i][0]->CANCELED_FEE_ADMIN = 0;
            endif;

            // Handler Null Canceled Total Fee
            if (null == $cekCancel[$i][0]->CANCELED_TOTAL_FEE) :
                $cekCancel[$i][0]->CANCELED_TOTAL_FEE = 0;
            endif;

            // Handler Null Canceled Fee Vsi
            if (null == $cekCancel[$i][0]->CANCELED_FEE_VSI) :
                $cekCancel[$i][0]->CANCELED_FEE_VSI = 0;
            endif;

            // Handler Null Canceled Biller Amount
            if (null == $cekCancel[$i][0]->CANCELED_BILLER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_BILLER_AMOUNT = 0;
            endif;

            // Save To Variable Canceled
            $canceled[$i][0] = $cekCancel[$i][0];
        }
        // *** END OF Logic Get Data Latest TRX, Paid, Canceled ***

        // *** Logic Mapping Data History Field ***
        for ($i=0; $i < $countHistory; $i++) {
            $data[$i] = collect($data[$i]);

            // Data Paid Untuk Function Maping
            $dataPaid = [
                'nama_field' => [
                    'PAID_NBILL',
                    'PAID_NMONTH',
                    'PAID_FEE',
                    'PAID_FEE_ADMIN',
                    'PAID_TOTAL_FEE',
                    'PAID_FEE_VSI',
                    'PAID_BILLER_AMOUNT',
                ],

                'value_field' => [
                    $paid[$i][0]['PAID_NBILL'],
                    $paid[$i][0]['PAID_NMONTH'],
                    $paid[$i][0]['PAID_FEE'],
                    $paid[$i][0]['PAID_FEE_ADMIN'],
                    $paid[$i][0]['PAID_TOTAL_FEE'],
                    $paid[$i][0]['PAID_FEE_VSI'],
                    $paid[$i][0]['PAID_BILLER_AMOUNT'],
                ],

                'jumlah_field' => 7,
            ];

            // Data Canceled Untuk Function Maping
            $dataCanceled = [
                'nama_field' => [
                    'CANCELED_NBILL',
                    'CANCELED_NMONTH',
                    'CANCELED_FEE',
                    'CANCELED_FEE_ADMIN',
                    'CANCELED_TOTAL_FEE',
                    'CANCELED_FEE_VSI',
                    'CANCELED_BILLER_AMOUNT',
                ],

                'value_field' => [
                    $canceled[$i][0]['CANCELED_NBILL'],
                    $canceled[$i][0]['CANCELED_NMONTH'],
                    $canceled[$i][0]['CANCELED_FEE'],
                    $canceled[$i][0]['CANCELED_FEE_ADMIN'],
                    $canceled[$i][0]['CANCELED_TOTAL_FEE'],
                    $canceled[$i][0]['CANCELED_FEE_VSI'],
                    $canceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                ],

                'jumlah_field' => 7,
            ];

            // Data Total Untuk Function Maping
            $field[$i] = [
                'TRX_NBILL' => $data[$i]['TRX_NBILL'],
                'TRX_NMONTH' => $data[$i]['TRX_NMONTH'],
                'TRX_FEE' => $data[$i]['TRX_FEE'],
                'TRX_FEE_ADMIN' => $data[$i]['TRX_FEE_ADMIN'],
                'TRX_TOTAL_FEE' => $data[$i]['TRX_TOTAL_FEE'],
                'TRX_FEE_VSI' => $data[$i]['TRX_FEE_VSI'],
                'TRX_BILLER' => $data[$i]['TRX_BILLER_AMOUNT'],

                'CANCELED_NBILL' => $canceled[$i][0]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $canceled[$i][0]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $canceled[$i][0]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$i][0]['CANCELED_FEE_ADMIN'],
                'CANCELED_TOTAL_FEE' => $canceled[$i][0]['CANCELED_TOTAL_FEE'],
                'CANCELED_FEE_VSI' => $canceled[$i][0]['CANCELED_FEE_VSI'],
                'CANCELED_BILLER' => $canceled[$i][0]['CANCELED_NBILLER_AMOUNT'],

                'PAID_NBILL' => $paid[$i][0]['PAID_NBILL'],
                'PAID_NMONTH' => $paid[$i][0]['PAID_NMONTH'],
                'PAID_FEE' => $paid[$i][0]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $paid[$i][0]['PAID_FEE_ADMIN'],
                'PAID_TOTAL_FEE' => $paid[$i][0]['PAID_TOTAL_FEE'],
                'PAID_FEE_VSI' => $paid[$i][0]['PAID_FEE_VSI'],
                'PAID_BILLER' => $paid[$i][0]['PAID_BILLER_AMOUNT'],
            ];

            $dataTotal = [
                'nama_field' => [
                    'TOTAL_NBILL',
                    'TOTAL_NMONTH',
                    'TOTAL_FEE',
                    'TOTAL_FEE_ADMIN',
                    'TOTAL_TOTAL_FEE',
                    'TOTAL_FEE_VSI',
                    'TOTAL_BILLER_AMOUNT',
                ],
                'value_field' => [
                    $field[$i]['TRX_NBILL'] + $field[$i]['CANCELED_NBILL'] - $field[$i]['PAID_NBILL'],
                    $field[$i]['TRX_NMONTH'] + $field[$i]['CANCELED_NMONTH'] - $field[$i]['PAID_NMONTH'],
                    $field[$i]['TRX_FEE'] + $field[$i]['CANCELED_FEE'] - $field[$i]['PAID_FEE'],
                    $field[$i]['TRX_FEE_ADMIN'] + $field[$i]['CANCELED_FEE_ADMIN'] - $field[$i]['PAID_FEE_ADMIN'],
                    $field[$i]['TRX_TOTAL_FEE'] + $field[$i]['CANCELED_TOTAL_FEE'] - $field[$i]['PAID_TOTAL_FEE'],
                    $field[$i]['TRX_FEE_VSI'] + $field[$i]['CANCELED_FEE_VSI'] - $field[$i]['PAID_FEE_VSI'],
                    $field[$i]['TRX_BILLER'] + $field[$i]['CANCELED_BILLER'] - $field[$i]['PAID_BILLER'],
                ],
                'jumlah_field' => 7,
            ];

            // Menghilangkan Jam Settled Dt
            $data[$i]->put('SETTLED_DT', substr($data[0]['SETTLED_DT'], 0, 10));

            // $this->mappingByProduct($data[$i], $dataTrx);
            $this->mappingByProduct($data[$i], $dataPaid);
            $this->mappingByProduct($data[$i], $dataCanceled);

            // Mapping Field Total
            $this->mappingByProduct($data[$i], $dataTotal);
        }
        // *** End Of Logic Mapping Data Field ***

        // Logic Maping Latest and History
        if (null != $countHistory) :
            $data->getCollection()
            ->prepend($latest[0]);
        endif;

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->generalDataResponse(200, 'Get List History Recon Dana Success', $data);
    }

    public function diffTransfer(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
                'correction' => ['required', 'numeric'],
                'correction_value' => ['required', 'string', "max:1"],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $correction = $request->correction;
        $value = $request->correction_value;

        // Check Data Recon Dana
        $checkDana = $this->reconDanaById($id);

        // Response Recon Dana Not Found
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get Data
        try {
            $data = ReconDana::select(
                'MODUL.CSC_GOP_PRODUCT_GROUP AS MODUL',
                'CSC_RDN_BILLER',
                'GTF.CSC_GTF_NAME AS GROUP_TRANSFER',
                'CSC_RDN_SETTLED_DT AS DATE_TRANSFER',
                'CSC_RDN_AMOUNT_TRANSFER AS AMOUNT_TRANSFER'
                //
                // CORRECTION -> request body
                // CORRECTION_VALUE -> request body
            )
            ->joinGroupTransfer()
            ->joinBiller()
            ->join(
                'CSCCORE_GROUP_OF_PRODUCT AS MODUL',
                'BILLER.CSC_BILLER_GROUP_PRODUCT',
                '=',
                'CSC_GOP_PRODUCT_GROUP'
            )
            ->id($id)
            ->first();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Different Transfer Recon Dana Failed', $th->getMessage());
        }

        // Mapping Correction and Correction Value
        $data = collect($data);
        $data->put('CORRECION', $correction);
        $data->put('CORRECION_VALUE', $value);

        // Response Success
        return $this->generalDataResponse(200, 'Get Data Different Transfer Recon Dana Success', $data);
    }

    public function addDiffTransfer(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'recon_id' => ['required', 'string', 'max:36'],
                'group_transfer' => ['required', 'string', 'max:50'],
                'date_transfer' => ['required', 'string', 'date_format:Y-m-d'],
                'correction' => ['required', 'numeric'],
                'correction_value' => ['required', 'string', 'max:1'],
                'amount_transfer' => ['required', 'numeric'],
                'desc' => ['required', 'string', 'max:100'],
                'created_by' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->recon_id;
        $groupTransfer = $request->group_transfer;
        $dateTransfer = $request->date_transfer;
        $correction = $request->correction;
        $value = $request->correction_value;
        $amount = $request->amount_transfer;
        $desc = $request->desc;
        $createdBy = $request->created_by;
        $uuid = Uuid::uuid4();
        $date = Carbon::now('Asia/Jakarta');

        // Check Data Recon Dana
        $checkDana = $this->reconDanaById($id);

        // Response Recon Dana Not Found
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Check Data Group Transfer
        $checkGtf = $this->groupTransferById($groupTransfer);

        // Response Group Transfer Not Found
        if (false == $checkGtf) :
            return $this->groupTransferNotFound();
        endif;

        // Check Status Deleted Recon Dana
        // $checkDanaDeleted = $this->reconDanaDeletedId($id);

        // Response Status Data Deleted
        // if (false == $checkDanaDeleted) :
        //     return $this->responseUnprocessable();
        // endif;

        // Logic Create Data
        try {
            $field = [
                'CSC_CORR_ID' => $uuid,
                'CSC_CORR_RECON_DANA_ID' => $id,
                'CSC_CORR_GROUP_TRANSFER' => $groupTransfer,
                'CSC_CORR_DATE' => $date,
                'CSC_CORR_DATE_TRANSFER' => $dateTransfer,
                'CSC_CORR_CORRECTION' => $correction,
                'CSC_CORR_CORRECTION_VALUE' => $value,
                'CSC_CORR_AMOUNT_TRANSFER' => $amount,
                'CSC_CORR_DESC' => $desc,
                'CSC_CORR_CREATED_BY' => $createdBy,
                'CSC_CORR_CREATED_DT' => $date,
            ];

            CoreCorrection::create($field);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Insert Data Correction Failed', $th->getMessage());
        }

        // Logic Update Correction Recon Dana
        try {
            // Inisialisasi Correction Recon Dana
            $reconDana = $this->reconDanaById($id);
            $correctionDana = $reconDana->CSC_RDN_CORRECTION_PROCESS;
            $valueDana = $reconDana->CSC_RDN_CORRECTION_PROCESS_VALUE;

            // Logic Summary Update Correction Recon Dana
            $correctionDana = ('-' == $valueDana) ? $correctionDana * -1 : $correctionDana;
            $correction = ('-' == $value) ? $correction * -1 : $correction;
            $updateCorrection = $correctionDana + $correction;

            // Logic Inisialisasi Variable
            $updateValue = (0 > $updateCorrection) ? '-' : '+';
            $updateCorrection = (0 > $updateCorrection) ? $updateCorrection * -1 : $updateCorrection;

            // Logic Update Data Correction Recon Dana
            $reconDana->CSC_RDN_CORRECTION_PROCESS = $updateCorrection;
            $reconDana->CSC_RDN_CORRECTION_PROCESS_VALUE = $updateValue;
            $reconDana->save();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Insert Data Correction Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Insert Data Correction Success');
    }

    public function getTransfer(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'string', 'max:36'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;

        // Check Data Recon Dana
        $checkDana = $this->reconDanaById($id);

        // Response Recon Dana Not Found
        if (false == $checkDana) :
            return $this->reconDanaNotFound();
        endif;

        // Logic Get Data
        try {
            $data = ReconDana::select(
                'CSC_RDN_BILLER AS BILLER',
                'CSC_RDN_GROUP_TRANSFER AS GROUP_TRANSFER',
                'CSC_RDN_START_DT AS START',
                'CSC_RDN_END_DT AS END',
                'CSC_RDN_SETTLED_DT AS DATE_TRANSFER',
                'CSC_RDN_DESC_TRANSFER AS DESC',
                'CSC_RDN_SUSPECT_UNPROCESS AS SUSPECT_UNPROCESS',
                'CSC_RDN_SUSPECT_UNPROCESS_VALUE AS SUSPECT_UNPROCESS_VALUE',
                'CSC_RDN_CORRECTION_UNPROCESS AS CORRECTION_UNPROCESS',
                'CSC_RDN_CORRECTION_UNPROCESS_VALUE AS CORRECTION_UNPROCESS_VALUE',
                DB::raw('SUM(CSC_RDN_SUSPECT_UNPROCESS + CSC_RDN_CORRECTION_UNPROCESS) AS AMOUNT_TRANSFER'),
            )
            ->id($id)
            ->first();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Additional Transfer Recon Dana Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalDataResponse(200, 'Get Data Additional Transfer Recon Dana Success', $data);
    }

    public function addTransfer(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'biller_id' => ['required', 'string', 'max:5'],
                'group_transfer' => ['required', 'string', 'max:50'],
                'start_dt' => ['required', 'string', 'max:10'],
                'end_dt' => ['required', 'string', 'max:10'],
                'settled_dt' => ['required', 'string', 'max:10'],
                'desc' => ['required', 'string', 'max:100'],
                'suspect_process' => ['required', 'numeric'],
                'suspect_process_value' => ['required', 'string', 'max:1'],
                'correction_process' => ['required', 'numeric'],
                'correction_process_value' => ['required', 'string', 'max:1'],
                'amount_transfer' => ['required', 'numeric'],
                'user_process' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $billerId = $request->biller_id;
        $groupTransfer = $request->group_transfer;
        $start = $request->start_dt;
        $end = $request->end_dt;
        $settled = $request->settled_dt;
        $desc = $request->desc;
        $process = $request->suspect_process;
        $processValue = $request->suspect_process_value;
        $correction = $request->correction_process;
        $correctionValue = $request->correction_process_value;
        $amount = $request->amount_transfer;
        $userProcess = $request->user_process;
        $status = 2;
        $type = 1;
        $uuid = uuid::uuid4();

        // Check Data Biller
        $checkBiller = $this->billerById($billerId);

        // Responnse Biller Not Found
        if (false == $checkBiller) :
            return $this->billerNotFound();
        endif;

        // Check Group Transfer
        $checkGtf = $this->groupTransferById($groupTransfer);

        // Response Group Transfer Not Found
        if (false == $checkGtf) :
            return $this->groupTransferNotFound();
        endif;

        // Logic Create Data
        try {
            $field = [
                'CSC_RDN_ID' => $uuid,
                'CSC_RDN_BILLER' => $billerId,
                'CSC_RDN_GROUP_TRANSFER' => $groupTransfer,
                'CSC_RDN_START_DT' => $start,
                'CSC_RDN_END_DT' => $end,
                'CSC_RDN_SETTLED_DT' => $settled,
                'CSC_RDN_DESC_TRANSFER' => $desc,
                'CSC_RDN_SUSPECT_PROCESS' => $process,
                'CSC_RDN_SUSPECT_PROCESS_VALUE' => $processValue,
                'CSC_RDN_CORRECTION_PROCESS' => $correction,
                'CSC_RDN_CORRECTION_PROCESS_VALUE' => $correctionValue,
                'CSC_RDN_AMOUNT_TRANSFER' => $amount,
                'CSC_RDN_USER_PROCESS' => $userProcess,
                'CSC_RDN_STATUS' => $status,
                'CSC_RDN_TYPE' => $type,
            ];

            ReconDana::create($field);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Additional Transfer Recon Dana Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Additional Transfer Recon Dana Success');
    }

    public function export(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'group_transfer' => ['required', 'array'],
                'group_transfer.*' => ['string', 'max:50'],
                'interval_date' => ['required', 'array'],
                'interval_date.*' => ['string', 'date_format:Y-m-d']
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->group_transfer;
        $countId = count($id);
        $notRegistered = [];
        $reconDanaId = [];
        $settledDt = [];
        $suspect = [];
        $dataSuspect = [];
        $product = [];

        // Validasi Data Group Transfer
        try {
            for ($i=0; $i < $countId; $i++) :
                $checkGroupTransfer = $this->groupTransferById($id[$i]);

                // Simpan Data Recon Dana yang tidak ditemukan
                if (false == $checkGroupTransfer) :
                    $notRegistered[] = $id[$i];
                    unset($id[$i]);
                endif;
            endfor;

            // Recounting & Reordering Request Id
            $id = array_values($id);
            $countId = count($id);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Validation Data Recon Dana Failed', $th->getMessage());
        }

        // Response Group Transfer Not Found
        if (null == $countId) :
            return $this->groupTransferNotFound();
        endif;

        // Logic Get Data
        try {
            for ($n=0; $n < $countId; $n++) :
                $data[] = $this->reconDanaListBiId($id[$n], 'gtf');
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Data Recon Dana Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $count = count($data);

        // Reordering Data Field and Store (Recon Dana ID, Settled Dt)
        for ($d=0; $d < $count; $d++) {
            // Menghitung jumlah data dari setiap group transfer
            $countData = count($data[$d]);

            for ($r=0; $r < $countData; $r++) {
                // Mapping Suspect and Correction
                $status = $data[$d][$r]['STATUS'];
                $type = $data[$d][$r]['TYPE'];

                /**
                 * Mapping Recon dana id dan Settled Dt
                 * Berfungsi sebagai key untuk mendapatkan data berikut:
                 * 1. Data Suspect
                 * 2. Data Corection
                 *
                 */

                $reconDanaId[] = $data[$d][$r]['ID'];
                $settledDt[] = [$data[$d][$r]['START'], $data[$d][$r]['END']];

                // Converting Status Field
                if (0 == $status) :
                    $status = 'Closed';
                elseif (1 == $status) :
                    $status = 'Waiting Process';
                elseif (2 == $status) :
                    $status = 'Processed';
                elseif (3 == $status) :
                    $status = 'Released';
                endif;

                // Converting Type Recon Dana
                if (0 == $type) :
                    $type = 'Main Transfer';
                elseif (1 == $type) :
                    $type = 'Additional Transfer';
                endif;

                // Implement Positive and Negative Nominal Value
                $valueSuspectProcess = $data[$d][$r]['SUSPECT_PROCESS_VALUE'];
                $valueSuspectUnprocess = $data[$d][$r]['SUSPECT_UNPROCESS_VALUE'];
                $valueCorrectionProcess = $data[$d][$r]['CORRECTION_PROCESS_VALUE'];
                $valueCorrectionUnprocess = $data[$d][$r]['CORRECTION_UNPROCESS_VALUE'];

                $data[$d][$r] = collect($data[$d][$r]);
                $data[$d][$r]->put('STATUS', $status);
                $data[$d][$r]->put('TYPE', $type);
                $data[$d][$r]->put('SUSPECT_PROCESS', $valueSuspectProcess.$data[$d][$r]['SUSPECT_PROCESS']);
                $data[$d][$r]->put('SUSPECT_UNPROCESS', $valueSuspectUnprocess.$data[$d][$r]['SUSPECT_UNPROCESS']);
                $data[$d][$r]->put('CORRECTION_PROCESS', $valueCorrectionProcess.$data[$d][$r]['CORRECTION_PROCESS']);
                $data[$d][$r]->put('CORRECTION_UNPROCESS', $valueCorrectionUnprocess.$data[$d][$r]['CORRECTION_UNPROCESS']);
                // Hapus Field Data yang tidak digunakan
                $data[$d][$r]->forget('ID');
                $data[$d][$r]->forget('BILLER_ID');
                $data[$d][$r]->forget('GROUP_TARNSFER_ID');
                $data[$d][$r]->forget('SUSPECT_PROCESS_VALUE');
                $data[$d][$r]->forget('SUSPECT_UNPROCESS_VALUE');
                $data[$d][$r]->forget('CORRECTION_PROCESS_VALUE');
                $data[$d][$r]->forget('CORRECTION_UNPROCESS_VALUE');
            }
        }

        // return $data;

        // return response()->json([
        //     'dana_id' => $reconDanaId,
        //     'settled_dt' => $settledDt,
        // ]);

        // Get Data Suspect by id and settled dt
        $countDana = count($reconDanaId);
        for ($a=0; $a < $countDana; $a++) {
            $suspect[] = $this->trxDanaIdSettledDt($reconDanaId[$a], $settledDt[$a]);
        }


        // Merge Data Suspect
        $suspect = collect($suspect);
        $countSuspect = count($suspect);

        // return $suspect;
        for ($m=0; $m < $countSuspect; $m++) {
            // Validasi Ketika Suspect Ditemukan
            if (false != $suspect[$m]) :
                // Inisialisasi Variable
                $countDataSuspect = count($suspect[$m]);

                // Menentukan Nama Product
                $product = $suspect[$m]->pluck('PRODUCT')->unique();
                $countProduct = count($product);

                // Mendapatkan Data Suspect Berdasarkan Product Tertentu
                for ($a=0; $a < $countProduct; $a++) :
                    $ratih[] = $suspect[$m]->where('PRODUCT', $product[$a])->all();
                    $countRatih = count($ratih);
                endfor;

                // Handle Array yang berubah menjadi object
                for ($u=0; $u < $countRatih; $u++) :
                    if (count($ratih[$u]) > 1) :
                        $salma[] = array_values($ratih[$u]);
                    else :
                        $salma[] = $ratih[$u];

                        if (0 == $ratih[$u][0]['STATUS']) {
                            $dataSuspect[$m] = [
                                'PRODUCT' => null,
                                'PAID_NBILL' => 0,
                                'PAID_NMONTH' => 0,
                                'PAID_RPTAG' => 0,
                                'PAID_ADMIN' => 0,
                                'PAID_ADMIN_AMOUNT' => 0,
                                'PAID_TOTAL' => 0,
                                'PAID_BILLER' => 0,
                                'PAID_BILLER_AMOUNT' => 0,
                                'PAID_VSI' => 0,
                                'PAID_VSI_AMOUNT' => 0,
                                'PAID_FORMULA_TRANSFER' => null,
                                'PAID_CLAIM_ADMIN' => 0,
                                'PAID_CLAIM_ADMIN_AMOUNT' => 0,
                                'PAID_CLAIM_PARTNER' => 0,
                                'PAID_CLAIM_PARTNER_AMOUNT' => 0,
                                // DIVIDER //
                                'CANCELED_NBILL' => 0,
                                'CANCELED_NMONTH' => 0,
                                'CANCELED_RPTAG' => 0,
                                'CANCELED_ADMIN' => 0,
                                'CANCELED_ADMIN_AMOUNT' => 0,
                                'CANCELED_TOTAL' => 0,
                                'CANCELED_BILLER' => 0,
                                'CANCELED_BILLER_AMOUNT' => 0,
                                'CANCELED_VSI' => 0,
                                'CANCELED_VSI_AMOUNT' => 0,
                                'CANCELED_FORMULA_TRANSFER' => null,
                                'CANCELED_CLAIM_ADMIN' => 0,
                                'CANCELED_CLAIM_ADMIN_AMOUNT' => 0,
                                'CANCELED_CLAIM_PARTNER' => 0,
                                'CANCELED_CLAIM_PARTNER_AMOUNT' => 0,
                            ];
                        }
                    endif;
                endfor;

                // Merging Data Suspect Sesuai dengan Suspect Product yang sudah ditemukan
                // for ($l=0; $l < $countDataSuspect; $l++) {
                // }

            endif;
        }

        // return $ratih[0][0]['STATUS'];

        // Inisialisasi Variable For Export
        $date = $data[0][0]['DATE_TRANSFER'];
        $fileName = 'RECONSILIATION_MULTI_BILLER_'. $date .'.xlsx';
        $file = base64_encode($fileName);
        $response['url'] = url('api/recon-data/export-download/'.$file);

        $data = collect($data);
        $dataGroupTransfer = $data->groupBy('GROUP_TRANSFER');
        return array_keys($data[0][0]->toArray());

        // Create File Export
        try {
            $arrayData = [
                'data_resume' => $data->toArray(),
                'data_group_transfer' => $dataGroupTransfer->toArray(),
            ];

            $parameter = [
                'title' => 'SUMMARY MULTI BILLER',
                'title_sheet' => $dataGroupTransfer->keys(),
                'header' => array_keys($data[0][0]->toArray()),
                'date' => $date,
            ];

            Excel::store(
                // Selesaikan data di Resume
                new ExportMultiBiller($arrayData, $parameter),
                $fileName,
                'xlsx_recon_dana',
                ExcelExcel::XLSX
            );
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Export Recon Dana Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notRegistered) :
            $response['id_not_registered'] = $notRegistered;
            return $this->generalDataResponse(
                202,
                'Export Recon Dana Success but Some Recon Dana Not Registered',
                $response
            );
        endif;

        // Response Success
        return $this->generalDataResponse(200, 'Export Summary Recon Dana Success', $response);
    }
}
