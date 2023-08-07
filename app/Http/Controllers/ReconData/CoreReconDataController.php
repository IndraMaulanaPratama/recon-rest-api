<?php

namespace App\Http\Controllers\ReconData;

use App\Exports\ReconDataExport;
use App\Http\Controllers\Controller;
use App\Models\CoreBiller;
use App\Models\CoreDownCentral;
use App\Models\CoreGroupOfProduct;
use App\Models\CoreReconData;
use App\Models\CoreReconDataHistory;
use App\Models\TransactionDefinitionV2;
use App\Models\TrxCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Traits\ResponseHandler;
use App\Traits\ProductTraits;
use App\Traits\CidTraits;
use App\Traits\ReconDataTraits;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class CoreReconDataController extends Controller
{
    use ProductTraits;
    use ResponseHandler;
    use CidTraits;
    use ReconDataTraits;

    public function getColumn()
    {
        return [
            'CSC_RDT_ID AS ID',
            'CSC_RDT_PRODUCT AS PRODUCT',
            'CSC_RDT_NBILL AS NBILL',
            'CSC_RDT_NMONTH AS NMONTH',
            'CSC_RDT_FEE AS FEE',
            'CSC_RDT_BILLER_AMOUNT AS FEE_ADMIN',
            'CSC_RDT_FEE_VSI AS FEE_VSI',
            'CSC_RDT_BILLER_AMOUNT AS BILLER_AMOUNT',
            'CSC_RDT_USER_SETTLED AS USER_SETTLED',
            'CSC_RDT_STATUS AS STATUS',
        ];
    }

    public function simpleColumn()
    {
        return [
            'CSC_RDT_ID AS ID',
            'CSC_RDT_PRODUCT AS PRODUCT',
            'CSC_RDT_USER_SETTLED AS USER_SETTLED',
            'CSC_RDT_STATUS AS STATUS',
        ];
    }

    public function updateStatus($id, $status, $user)
    {
        $reconData = CoreReconData::searchData($id)->first();
        $reconData->CSC_RDT_USER_SETTLED = $user;
        $reconData->CSC_RDT_STATUS = $status;
        $reconData->save();

        if ($reconData) {
            return true;
        } else {
            return false;
        }
    }

    public function mappingByProduct($data, $mapping)
    {
        $jumlahField = $mapping['jumlah_field'];
        $namaField = $mapping['nama_field'];
        $valueField = $mapping['value_field'];

        for ($map=0; $map < $jumlahField; $map++) {
            $data->put($namaField[$map], $valueField[$map]);
        }
    }

    public function list(Request $request)
    {
        try {
            // Validasi Data Mandatory
            $request->validate([
                'start' => ['required', 'string', 'max:10'],
                'end' => ['required', 'string', 'max:10'],
                'modul' => ['required', 'string', 'max:50'],
                'biller' => ['required', 'string', 'max:50'],
            ]);

            // Inisialisasi Variable yang di perlukan
            $modulName = (false == $request->modul) ? null : $request->modul;
            $billerName = (false == $request->biller) ? null : $request->biller;
            $date = [$request->start, $request->end];
            $items = (null == $request->items) ? 10 : $request->items;

            // Cek Modul/Product
            if (null != $modulName) :
                $cekModul = CoreGroupOfProduct::modul($modulName)->first();
                if (null == $cekModul) {
                    return $this->generalResponse(
                        404,
                        'Data Modul Not Found'
                    );
                }
            endif;

            // Cek Biller
            if (null != $billerName) :
                $cekBiller = CoreBiller::groupBiller($billerName)->first();
                if (null == $cekBiller) {
                    return $this->generalResponse(
                        404,
                        'Data Biller Not Found'
                    );
                }
            endif;

            // Baca Recon Data
            try {
                $data = CoreReconData::select(
                    'TD.CSC_TD_NAME AS PRODUCT',
                    DB::raw('SUM(CSC_RDT_NBILL) AS NBILL'),
                    DB::raw('SUM(CSC_RDT_NMONTH) AS NMONTH'),
                    DB::raw('SUM(CSC_RDT_FEE) AS FEE'),
                    DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS FEE_ADMIN'),
                    DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT + CSC_RDT_FEE) AS TOTAL'),
                    DB::raw('SUM(CSC_RDT_FEE_BILLER) AS FEE_BILLER'),
                    DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_FEE_VSI) AS FEE_VSI'),
                    DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS BILLER_AMOUNT'),
                    'FH.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                    DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS CLAIM_VSI'),
                    DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS CLAIM_PARTNER'),
                    DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
                    'CSC_RDT_USER_SETTLED AS USER_SETTLED',
                    'CSC_RDT_STATUS AS STATUS',
                    'CSC_RDT_STATUS AS STATUS_SUSPECT',
                )
                ->join(
                    'CSCCORE_FORMULA_TRANSFER AS FH',
                    'CSC_RDT_FORMULA_TRANSFER',
                    '=',
                    'FH.CSC_FH_ID'
                )
                ->join(
                    'CSCCORE_TRANSACTION_DEFINITION AS TD',
                    'TD.CSC_TD_NAME',
                    '=',
                    'CSC_RDT_PRODUCT'
                )
                ->join(
                    'CSCCORE_BILLER_PRODUCT AS BP',
                    'BP.CSC_BP_PRODUCT',
                    '=',
                    'TD.CSC_TD_NAME'
                )
                ->join(
                    'CSCCORE_BILLER AS BILLER',
                    'BP.CSC_BP_BILLER',
                    '=',
                    'BILLER.CSC_BILLER_ID'
                )
                ->dateRange($date)
                ->where(
                    function ($query) use ($modulName, $billerName) {
                        if (null != $modulName) :
                            $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modulName);
                        endif;

                        if (null != $billerName) :
                            $query->where('GOP.CSC_GOP_PRODUCT_PARENT_PRODUCT', $billerName);
                        endif;
                    }
                )
                ->groupBy('CSC_RDT_PRODUCT')
                ->groupBy('CSC_RDT_STATUS')
                ->paginate($items);
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Get List Recon Data Failed', $th->getMessage());
            }
            // End Of Baca Recon Data

            // Hitung Jumlah Data
            $count = count($data);

            // Response Ketika Data Tidak Ditemukan / 404
            if (null == $count) {
                return $this->generalResponse(
                    404,
                    'List Recon Data Not Found'
                );
            }

            // Check Status Suspect -> Mapping Suspect
            try {
                for ($i=0; $i < $count; $i++) :
                    $product = $data[$i]['PRODUCT'];
                    $statusSuspect = $this->reconDataCheckSuspect($product, $date);

                    // Mapping Status Suspect
                    $data[$i] = collect($data[$i]);
                    $data[$i]->put('STATUS_SUSPECT', $statusSuspect);
                endfor;
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Get List Recon Data Failed', $th->getMessage());
            }

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Ketika Berhasil / 200
            if (null != count($data)) {
                return $this->generalDataResponse(
                    200,
                    'Get List Recon Data Success',
                    $data
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
        try {
            // Validasi Data Mandatory
            $request->validate([
                'start' => ['required', 'string', 'max:10'],
                'end' => ['required', 'string', 'max:10'],
                'modul' => ['required', 'string', 'max:50'],
                'biller' => ['required', 'string', 'max:50'],
            ]);

            // Inisialisasi Variable yang di perlukan
            $modulName = (false == $request->modul) ? null : $request->modul;
            $billerName = (false == $request->biller) ? null : $request->biller;
            $date = [$request->start, $request->end];
            $items = (null == $request->items) ? 10 : $request->items;
            $status = (null == $request->status) ? null : $request->status;

            // Cek Modul/Product
            if (null != $modulName) :
                $cekModul = CoreGroupOfProduct::modul($modulName)->first();
                if (null == $cekModul) {
                    return $this->generalResponse(
                        404,
                        'Data Modul Not Found'
                    );
                }
            endif;

            // Cek Biller
            if (null != $billerName) :
                $cekBiller = CoreBiller::groupBiller($billerName)->first();
                if (null == $cekBiller) {
                    return $this->generalResponse(
                        404,
                        'Data Biller Not Found'
                    );
                }
            endif;

            // Baca Recon Data
            try {
                $data = CoreReconData::select(
                    'TD.CSC_TD_NAME AS PRODUCT',
                    DB::raw('SUM(CSC_RDT_NBILL) AS NBILL'),
                    DB::raw('SUM(CSC_RDT_NMONTH) AS NMONTH'),
                    DB::raw('SUM(CSC_RDT_FEE) AS FEE'),
                    DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS FEE_ADMIN'),
                    DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT + CSC_RDT_FEE) AS TOTAL'),
                    DB::raw('SUM(CSC_RDT_FEE_BILLER) AS FEE_BILLER'),
                    DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_FEE_VSI) AS FEE_VSI'),
                    DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS BILLER_AMOUNT'),
                    'FH.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                    DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS CLAIM_VSI'),
                    DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
                    DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS CLAIM_PARTNER'),
                    DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
                    'CSC_RDT_USER_SETTLED AS USER_SETTLED',
                    'CSC_RDT_STATUS AS STATUS',
                    'CSC_RDT_STATUS AS STATUS_SUSPECT',
                )
                ->join(
                    'CSCCORE_FORMULA_TRANSFER AS FH',
                    'CSC_RDT_FORMULA_TRANSFER',
                    '=',
                    'FH.CSC_FH_ID'
                )
                ->join(
                    'CSCCORE_TRANSACTION_DEFINITION AS TD',
                    'TD.CSC_TD_NAME',
                    '=',
                    'CSC_RDT_PRODUCT'
                )
                ->join(
                    'CSCCORE_BILLER_PRODUCT AS BP',
                    'BP.CSC_BP_PRODUCT',
                    '=',
                    'TD.CSC_TD_NAME'
                )
                ->join(
                    'CSCCORE_BILLER AS BILLER',
                    'BP.CSC_BP_BILLER',
                    '=',
                    'BILLER.CSC_BILLER_ID'
                )
                ->dateRange([$request->start, $request->end])
                ->where(
                    function ($query) use ($modulName, $billerName, $status) {
                        if (null != $modulName) :
                            $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $modulName);
                        endif;

                        if (null != $billerName) :
                            $query->where('GOP.CSC_GOP_PRODUCT_PARENT_PRODUCT', $billerName);
                        endif;

                        if (null != $status) :
                            $query->where('CSC_RDT_STATUS', $status);
                        endif;
                    }
                )
                ->groupBy('CSC_RDT_PRODUCT')
                ->groupBy('CSC_RDT_STATUS')
                ->paginate($items);
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Filter Recon Data Failed', $th->getMessage());
            }
            // End Of Baca Recon Data

            // Hitung Data
            $count = count($data);

            // Response Ketika Data Tidak Ditemukan / 404
            if (null == $count) {
                return $this->generalResponse(
                    404,
                    'Recon Data Not Found'
                );
            }

            // Check Status Suspect -> Mapping Suspect
            try {
                for ($i=0; $i < $count; $i++) :
                    $product = $data[$i]['PRODUCT'];
                    $statusSuspect = $this->reconDataCheckSuspect($product, $date);

                    // Mapping Status Suspect
                    $data[$i] = collect($data[$i]);
                    $data[$i]->put('STATUS_SUSPECT', $statusSuspect);
                endfor;
            } catch (\Throwable $th) {
                return $this->responseDataFailed('Get List Recon Data Failed', $th->getMessage());
            }

            // Add Index Number
            $data = $this->addIndexNumber($data);

            // Response Ketika Berhasil / 200
            if (null != count($data)) {
                return $this->generalDataResponse(
                    200,
                    'Filter Recon Data Success',
                    $data
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

    public function settledProduct(Request $request)
    {
        try {
            // Validasi Data Mandatory
            $request->validate([
                'settled_by' => ['required', 'string', 'max:50'],
                'interval_date' => ['required', 'array', 'min:1'],
                'interval_date.*' => ['string', 'date_format:Y-m-d'],
                'product' => ['required', 'array', 'min:1'],
                'product.*.name' => ['required', 'string', 'max:100'],
                'product.*.status' => ['required', 'numeric', 'digits:1'],
            ]);
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable yang dibutuhkan
        $product = $request->product;
        $user = $request->settled_by;
        $date = $request->interval_date;
        $countProduct = count($product);
        $notFound = [];
        $productNotFound = [];
        $productFound = [];
        $response = [];
        $hasilProses = 1;
        $processed = [];
        $dataProduct = null;
        $countData = null;

        // Validasi Data Product
        for ($i=0; $i < $countProduct; $i++) {
            $cekProduct[$i] = $this->reconDataSearchProductStatus($product[$i]['name'], $product[$i]['status']);
            if (false == $cekProduct[$i]) :
                $productNotFound[] = $product[$i];
                unset($product[$i]);
            else :
                $productFound[] = $product[$i];
            endif;
        }

        // Response Ketika Tidak ada Product yang terbaca / 404
        if ($countProduct == count($productNotFound)) {
            return $this->generalResponse(
                404,
                'Product Recon Data Not Found'
            );
        }

        // Reinisiasi Variable Product yang ditemukan
        $product = $productFound;
        $countProduct = count($product);

        // Validasi Data Not Found
        for ($i=0; $i < $countProduct; $i++) {
            $dataProduct[] = CoreReconData::select(
                'CSC_RDT_ID AS ID',
            )
            ->dateRange($date)
            ->product($product[$i])
            ->first();

            // Handle Product yang tidak ditemukan pada table recon data
            if (null == $dataProduct[$i]) :
                $notFound[] = $product[$i];
                unset($product[$i]);
            endif;
        }

        // Reinisiasi Variable Product yang ditemukan
        $product = array_values($product);
        $countProduct = count($product);
        $dataProduct = null;

        // Validasi Data Processed
        for ($i=0; $i < $countProduct; $i++) {
            $dataProduct[] = CoreReconData::select(
                'CSC_RDT_ID AS ID',
                'CSC_RDT_PRODUCT AS PRODUCT',
                'CSC_RDT_STATUS AS STATUS',
            )
            ->status(1)
            ->dateRange($date)
            ->product($product[$i])
            ->first();

            // Handle Data yang sedang di process
            if (null != $dataProduct[$i]) :
                $processed[] = $product[$i];
                unset($product[$i]);
            endif;
        }

        // Reinisiasi Variable Product yang sedang diprocess
        $product = array_values($product);
        $countProduct = count($product);
        $dataProduct = null;

        // Logic Proses Update Settle
        for ($i=0; $i < $countProduct; $i++) {
            // Cari Data Berdasarkan Nama Product dan Range Tanggal
            $dataProduct[] = CoreReconData::select(
                'CSC_RDT_ID AS ID',
                'CSC_RDT_PRODUCT AS PRODUCT',
                'CSC_RDT_STATUS AS STATUS',
                'CSC_RDT_USER_SETTLED',
            )
            ->dateRange($date)
            ->status(2)
            ->orWhere('CSC_RDT_STATUS', 0)
            ->product($product[$i])
            ->get();

            // Hitung Jumlah Data
            $countData = count($dataProduct[$i]);

            // Logic Update Status
            if (null != $countData) :
                // Proses Perubahan User dan Status Sattle
                $countData = $countData;
                $data = $dataProduct[$i];

                for ($j=0; $j < $countData; $j++) {
                    $updateStatus = $this->updateStatus(
                        $data[$j]->ID,
                        1,
                        $user
                    );

                    if ($updateStatus) {
                        $hasilProses = true;
                    } else {
                        $hasilProses = false;
                    }
                }
            endif;
        }
        // End Of Logic Proses Update Settle

        // return response()->json([
        //     'count_data' => $countData,
        //     'product_not_found' => $productNotFound,
        //     'recon_not_found' => $notFound,
        //     'processed' => $processed,
        //     'data_product' => $dataProduct,
        // ]);

        // Response Ketika Proses Update Status Gagal / 500
        if (false == $hasilProses) {
            return $this->generalResponse(
                500,
                'Settled Data Failed'
            );
        }

        // Response Ketika Semua Proses Berhasil / 200
        if (null == $notFound
        && null == $productNotFound
        && null == $processed) :
            return $this->generalResponse(
                200,
                'Settled Data Success'
            );
        endif;

        // inisialisasi status Response 202
        $status = 202;

        // Response Ketika Product tidak ditemukan pada table Recon / 202
        if (null != $notFound
        && null == $productNotFound
        && null == $processed) :
            $message = "Settle Data Success but Some Product Between The Selected Transaction Dates Not Found";
            $response['product_not_found'] = $notFound;

            return $this->generalDataResponse($status, $message, $response);
        endif;

        // Response Ketika Product tidak ditemukan pada table Product / 202
        if (null != $productNotFound
        && null == $notFound
        && null == $processed) :
            $message = "Settle Data Success but Some Product Not Registered";
            $response['product_not_registered'] = $productNotFound;

            return $this->generalDataResponse($status, $message, $response[0]);
        endif;

        // Response Ketika Product Sedang dalam process / 202
        if (null == $notFound
        && null == $productNotFound
        && null != $processed) :
            $message = "Product is being processed, please be patient";
            $response['product_processed'] = $processed;

            return $this->generalDataResponse($status, $message, $response);
        endif;

        // Response Cannot Process / 202
        if (null != $notFound
        || null != $productNotFound
        || null != $processed) :
            $message = "Settle Data Success but Some Product Cannot Processed";

            (null == $productNotFound) ?: $response['product_not_registered'] = $productNotFound;
            (null == $notFound) ?: $response['product_not_found'] = $notFound;
            (null == $processed) ?: $response['product_processed'] = $processed;

            return $this->generalDataResponse($status, $message, $response);
        endif;
    }

    public function listSuspect(Request $request)
    {
        // Validasi Request Mandatory
        try {
            $request->validate([
                'product' => ['required', 'string', 'max:100'],
                'interval_date' => ['required', 'array', 'min:1'],
                'interval_date.*' => ['string', 'date_format:Y-m-d'],
            ]);
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Definisi Variable yang dibutuhkan
        $product = $request->product;
        $date = $request->interval_date;
        $items = (null == $request->items) ? 10 : $request->items;

        // Cek Product
        $cekProduct = TransactionDefinitionV2::searchData($product)->first();
        if (null == $cekProduct) :
            return $this->generalResponse(
                404,
                'Data Product/Area Not Found'
            );
        endif;

        // Cek Recon Data
        $reconData = CoreReconData::product($product)->first();
        if (null == $reconData) :
            return $this->generalResponse(
                404,
                'Data Recon-Data Not Found'
            );
        endif;

        // *** Logic Get Data Suspect ***
        try {
            $data = TrxCorrection::select(
                'CSM_TC_ID AS ID',
                'CSM_TC_CID AS CID',
                'CSM_TC_TRX_DT AS TRX_DATE',
                'CSM_TC_PROCESS_DT AS PROCESS_DT',
                //
                DB::raw('SUM(CSM_TC_NBILL) AS NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS TOTAL'),
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS BILLER_AMOUNT'),
                'FT.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
                //
                'CSM_TC_SW_REFNUM AS SW_REFNUM',
                //
                'CSM_TC_STATUS_TRX AS STATUS_TRX',
                'CSM_TC_STATUS_DATA AS STATUS_DATA',
                'CSM_TC_STATUS_FUNDS AS STATUS_FUNDS',
                //
                'TD.CSC_TD_TABLE',
                'TD.CSC_TD_SUBID_COLUMN',
                'TD.CSC_TD_SUBNAME_COLUMN',
                'TD.CSC_TD_SWITCH_REFNUM_COLUMN',
                'TD.CSC_TD_TERMINAL_COLUMN',
                'CSM_TC_SUBID',
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->join(
                'CSCCORE_TRANSACTION_DEFINITION AS TD',
                'TD.CSC_TD_NAME',
                '=',
                'CSM_TC_PRODUCT'
            )
            ->where('CSM_TC_PRODUCT', $product)
            ->whereNull('CSM_TC_RECON_ID')
            ->whereBetween('CSM_TC_TRX_DT', $date)
            ->groupBy('CSM_TC_ID')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List Suspect Recon Data Failed', $th->getMessage());
        }
        // *** End Of Logic Get Data Suspect ***

        // Logic Get Data Tran Main
        $countData = count($data);
        for ($i=0; $i < $countData; $i++) {
            // Inisialisasi Vaiable
            $tableName[$i] = $data[$i]->CSC_TD_TABLE;
            $subidColumn[$i] = $data[$i]->CSC_TD_SUBID_COLUMN;
            $subNameColumn[$i] = $data[$i]->CSC_TD_SUBNAME_COLUMN;
            $switchRefnumColumn[$i] = $data[$i]->CSC_TD_SWITCH_REFNUM_COLUMN;
            $ppidColumn[$i] = $data[$i]->CSC_TD_TERMINAL_COLUMN;
            $subid[$i] = $data[$i]->CSM_TC_SUBID;
            $refnum[$i] = $data[$i]->CSM_TC_SW_REFNUM;

            // Koneksi ke Server Recon
            $trainMain[$i] = DB::connection('server_recon')
            ->table($tableName[$i])
            ->where($subidColumn[$i], $subid[$i])
            ->first();

            // Mapping Data Tran Main
            if (null != $trainMain[$i]) :
                $data[$i] = collect($data[$i]);
                $data[$i]->put('CUSTOMER_ID', $trainMain[$i]->CSM_TM_SUBID);
                $data[$i]->put('CUSTOMER_NAME', $trainMain[$i]->CSM_TM_NAME);
                $data[$i]->put('PPID', $trainMain[$i]->CSM_TM_PPID);
                $data[$i]->put('PPID_SUB_CA', null);
                $data[$i]->put('PPID_CA', null);
                $data[$i]->forget('CSC_TD_TABLE');
                $data[$i]->forget('CSM_TC_RECON_ID');
                $data[$i]->forget('CSC_TD_SUBID_COLUMN');
                $data[$i]->forget('CSC_TD_SUBNAME_COLUMN');
                $data[$i]->forget('CSC_TD_SWITCH_REFNUM_COLUMN');
                $data[$i]->forget('CSM_TC_SUBID');
                $data[$i]->forget('CSC_TD_TERMINAL_COLUMN');
            else :
                $data[$i] = collect($data[$i]);
                $data[$i]->put('CUSTOMER_ID', null);
                $data[$i]->put('CUSTOMER_NAME', null);
                $data[$i]->put('PPID', null);
                $data[$i]->put('PPID_SUB_CA', null);
                $data[$i]->put('PPID_CA', null);
                $data[$i]->forget('CSC_TD_TABLE');
                $data[$i]->forget('CSM_TC_RECON_ID');
                $data[$i]->forget('CSC_TD_SUBID_COLUMN');
                $data[$i]->forget('CSC_TD_SUBNAME_COLUMN');
                $data[$i]->forget('CSC_TD_SWITCH_REFNUM_COLUMN');
                $data[$i]->forget('CSM_TC_SUBID');
                $data[$i]->forget('CSC_TD_TERMINAL_COLUMN');
            endif;
        }

        // Response Not Found
        if (null == count($data)) {
            return $this->generalResponse(
                404,
                'Data Suspect Recon Data Not Found'
            );
        }

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Sukses
        if (null != count($data)) {
            return $this->generalDataResponse(
                200,
                'Get List Suspect Recon Data Success',
                $data
            );
        }
    }

    public function listByProduct(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(
                [
                    'product' => ['required', 'string', 'max:100'],
                    'interval_date' => ['required', 'array', 'min:1', 'max:2'],
                    'interval_date.*' => ['date_format:Y-m-d'],
                    'status' => ['required', 'numeric', 'digits:1'],
                    'items' => ['numeric', 'digits_between:1,8'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                '400',
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $product = $request->product;
        $status = $request->status;
        $date = $request->interval_date;
        $items = (null == $request->items) ? 10 : $request->items;
        $cekPaid = [];
        $cekCancel = [];
        $paid = [];
        $canceled = [];

        // Cek Product
        $cekProduct = TransactionDefinitionV2::searchData($product)->first('CSC_TD_NAME');
        if (null == $cekProduct) {
            return $this->generalResponse(
                404,
                'Data Product/Area Not Found'
            );
        }

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
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE+CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDT_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSC_RDT_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->product($product)
            ->status($status)
            ->dateRange($date)
            ->groupBy('CID')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by Product Recon Data Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $countData = count($data);

        // Logic Get Data Paid dan Cancelled
        for ($i=0; $i < $countData; $i++) {
            // Get Data CID
            $cid[$i] = $data[$i]->CID;

            // Logic Get Data Paid
            $cekPaid[$i] = TrxCorrection::select(
                DB::raw('SUM(CSM_TC_NBILL) AS PAID_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS PAID_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS PAID_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS PAID_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS PAID_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS PAID_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS PAID_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS PAID_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS PAID_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS PAID_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS PAID_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS PAID_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS PAID_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS PAID_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->whereNull('CSM_TC_RECON_ID')
            ->where('CSM_TC_PRODUCT', $product)
            ->where('CSM_TC_CID', $cid[$i])
            ->whereBetween('CSM_TC_TRX_DT', $date)
            ->where('CSM_TC_STATUS_TRX', 0)
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

            // Handler Null Paid Fee Admin Amount
            if (null == $cekPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT) :
                $cekPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT = 0;
            endif;

            // Handler Null Paid Fee Vsi
            if (null == $cekPaid[$i][0]->PAID_FEE_VSI) :
                $cekPaid[$i][0]->PAID_FEE_VSI = 0;
            endif;

            // Handler Null Paid Fee Vsi Amount
            if (null == $cekPaid[$i][0]->PAID_FEE_VSI_AMOUNT) :
                $cekPaid[$i][0]->PAID_FEE_VSI_AMOUNT = 0;
            endif;

            // Handler Null Paid Total Fee
            if (null == $cekPaid[$i][0]->PAID_TOTAL_FEE) :
                $cekPaid[$i][0]->PAID_TOTAL_FEE = 0;
            endif;

            // Handler Null Paid Fee Biller
            if (null == $cekPaid[$i][0]->PAID_FEE_BILLER) :
                $cekPaid[$i][0]->PAID_FEE_BILLER = 0;
            endif;

            // Handler Null Paid Fee Biller Amount
            if (null == $cekPaid[$i][0]->PAID_FEE_BILLER_AMOUNT) :
                $cekPaid[$i][0]->PAID_FEE_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Paid Claim VSI
            if (null == $cekPaid[$i][0]->PAID_CLAIM_VSI) :
                $cekPaid[$i][0]->PAID_CLAIM_VSI = 0;
            endif;

            // Handler Null Paid Claim VSI Amount
            if (null == $cekPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT) :
                $cekPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT = 0;
            endif;

            // Handler Null Paid Biller Amount
            if (null == $cekPaid[$i][0]->PAID_BILLER_AMOUNT) :
                $cekPaid[$i][0]->PAID_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Paid Claim Partner
            if (null == $cekPaid[$i][0]->PAID_CLAIM_PARTNER) :
                $cekPaid[$i][0]->PAID_CLAIM_PARTNER = 0;
            endif;

            // Handler Null Paid Claim Partner Amount
            if (null == $cekPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT) :
                $cekPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT = 0;
            endif;

            // Save To Variable Paid
            $paid[$i][0] = $cekPaid[$i][0];

            // Logic Get Data Canceled
            $cekCancel[$i] = TrxCorrection::select(
                DB::raw('SUM(CSM_TC_NBILL) AS CANCELED_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CANCELED_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CANCELED_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CANCELED_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CANCELED_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS CANCELED_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS CANCELED_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS CANCELED_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS CANCELED_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CANCELED_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CANCELED_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CANCELED_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CANCELED_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CANCELED_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->where('CSM_TC_PRODUCT', $product)
            ->where('CSM_TC_CID', $cid[$i])
            ->whereBetween('CSM_TC_TRX_DT', $date)
            ->where('CSM_TC_STATUS_TRX', 1)
            ->get();

            // Handler Null Canceled Nbill
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

            // Handler Null Canceled Fee Admin Amount
            if (null == $cekCancel[$i][0]->CANCELED_FEE_ADMIN_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_FEE_ADMIN_AMOUNT = 0;
            endif;

            // Handler Null Canceled Fee Vsi
            if (null == $cekCancel[$i][0]->CANCELED_FEE_VSI) :
                $cekCancel[$i][0]->CANCELED_FEE_VSI = 0;
            endif;

            // Handler Null Canceled Fee Vsi Amount
            if (null == $cekCancel[$i][0]->CANCELED_FEE_VSI_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_FEE_VSI_AMOUNT = 0;
            endif;

            // Handler Null Canceled Total Fee
            if (null == $cekCancel[$i][0]->CANCELED_TOTAL_FEE) :
                $cekCancel[$i][0]->CANCELED_TOTAL_FEE = 0;
            endif;

            // Handler Null Canceled Fee Biller
            if (null == $cekCancel[$i][0]->CANCELED_FEE_BILLER) :
                $cekCancel[$i][0]->CANCELED_FEE_BILLER = 0;
            endif;

            // Handler Null Canceled Fee Biller Amount
            if (null == $cekCancel[$i][0]->CANCELED_FEE_BILLER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_FEE_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Canceled Claim Vsi
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_VSI) :
                $cekCancel[$i][0]->CANCELED_CLAIM_VSI = 0;
            endif;

            // Handler Null Canceled Claim Vsi Amount
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_VSI_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_CLAIM_VSI_AMOUNT = 0;
            endif;

            // Handler Null Canceled Biller Amount
            if (null == $cekCancel[$i][0]->CANCELED_BILLER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Canceled Claim Partner
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER) :
                $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER = 0;
            endif;

            // Handler Null Canceled Claim Partner Amount
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT = 0;
            endif;


            // Save To Variable Canceled
            $canceled[$i][0] = $cekCancel[$i][0];
        }

        // Hitung Data Paid
        $countPaid = count($paid);

        // Hitung Data Canceled
        $countCanceled = count($canceled);

        // Response Not Found
        if (null == $countData) {
            return $this->generalResponse(
                404,
                'Data List by Product Recon Data Not Found'
            );
        }

        // *** Logic Mapping Field ***
        for ($i=0; $i < $countData; $i++) {
            $data[$i] = collect($data[$i]);

            // Data Paid Untuk Function Maping
            $dataPaid = [
                'nama_field' => [
                    'PAID_NBILL',
                    'PAID_NMONTH',
                    'PAID_FEE',
                    'PAID_FEE_ADMIN',
                    'PAID_FEE_ADMIN_AMOUNT',
                    'PAID_FEE_VSI',
                    'PAID_FEE_VSI_AMOUNT',
                    'PAID_TOTAL_FEE',
                    'PAID_FORMULA_TRANSFER',
                    'PAID_FEE_BILLER',
                    'PAID_FEE_BILLER_AMOUNT',
                    'PAID_CLAIM_VSI',
                    'PAID_CLAIM_VSI_AMOUNT',
                    'PAID_BILLER_AMOUNT',
                    'PAID_CLAIM_PARTNER',
                    'PAID_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $paid[$i][0]->PAID_NBILL,
                    $paid[$i][0]->PAID_NMONTH,
                    $paid[$i][0]->PAID_FEE,
                    $paid[$i][0]->PAID_FEE_ADMIN,
                    $paid[$i][0]->PAID_FEE_ADMIN_AMOUNT,
                    $paid[$i][0]->PAID_FEE_VSI,
                    $paid[$i][0]->PAID_FEE_VSI_AMOUNT,
                    $paid[$i][0]->PAID_TOTAL_FEE,
                    $paid[$i][0]->PAID_FORMULA_TRANSFER,
                    $paid[$i][0]->PAID_FEE_BILLER,
                    $paid[$i][0]->PAID_FEE_BILLER_AMOUNT,
                    $paid[$i][0]->PAID_CLAIM_VSI,
                    $paid[$i][0]->PAID_CLAIM_VSI_AMOUNT,
                    $paid[$i][0]->PAID_BILLER_AMOUNT,
                    $paid[$i][0]->PAID_CLAIM_PARTNER,
                    $paid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT,
                ],

                'jumlah_field' => 15,
            ];

            // Data Canceled Untuk Function Maping
            $dataCanceled = [
                'nama_field' => [
                    'CANCELED_NBILL',
                    'CANCELED_NMONTH',
                    'CANCELED_FEE',
                    'CANCELED_FEE_ADMIN',
                    'CANCELED_FEE_ADMIN_AMOUNT',
                    'CANCELED_FEE_VSI',
                    'CANCELED_FEE_VSI_AMOUNT',
                    'CANCELED_TOTAL_FEE',
                    'CANCELED_FORMULA_TRANSFER',
                    'CANCELED_FEE_BILLER',
                    'CANCELED_FEE_BILLER_AMOUNT',
                    'CANCELED_CLAIM_VSI',
                    'CANCELED_CLAIM_VSI_AMOUNT',
                    'CANCELED_BILLER_AMOUNT',
                    'CANCELED_CLAIM_PARTNER',
                    'CANCELED_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $canceled[$i][0]->CANCELED_NBILL,
                    $canceled[$i][0]->CANCELED_NMONTH,
                    $canceled[$i][0]->CANCELED_FEE,
                    $canceled[$i][0]->CANCELED_FEE_ADMIN,
                    $canceled[$i][0]->CANCELED_FEE_ADMIN_AMOUNT,
                    $canceled[$i][0]->CANCELED_FEE_VSI,
                    $canceled[$i][0]->CANCELED_FEE_VSI_AMOUNT,
                    $canceled[$i][0]->CANCELED_TOTAL_FEE,
                    $canceled[$i][0]->CANCELED_FORMULA_TRANSFER,
                    $canceled[$i][0]->CANCELED_FEE_BILLER,
                    $canceled[$i][0]->CANCELED_FEE_BILLER_AMOUNT,
                    $canceled[$i][0]->CANCELED_CLAIM_VSI,
                    $canceled[$i][0]->CANCELED_CLAIM_VSI_AMOUNT,
                    $canceled[$i][0]->CANCELED_BILLER_AMOUNT,
                    $canceled[$i][0]->CANCELED_CLAIM_PARTNER,
                    $canceled[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT,
                ],

                'jumlah_field' => 15,
            ];

            // Data Total Untuk Function Maping
            $field[$i] = [
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

                'CANCELED_NBILL' => $canceled[$i][0]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $canceled[$i][0]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $canceled[$i][0]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$i][0]['CANCELED_FEE_ADMIN'],
                'CANCELED_FEE_ADMIN_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_ADMIN_AMOUNT'],
                'CANCELED_FEE_VSI' => $canceled[$i][0]['CANCELED_FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_VSI_AMOUNT'],
                'CANCELED_TOTAL_FEE' => $canceled[$i][0]['CANCELED_TOTAL_FEE'],
                'CANCELED_FEE_BILLER' => $canceled[$i][0]['CANCELED_FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_BILLER_AMOUNT'],
                'CANCELED_CLAIM_VSI' => $canceled[$i][0]['CANCELED_CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$i][0]['CANCELED_CLAIM_VSI_AMOUNT'],
                'CANCELED_BILLER' => $canceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $canceled[$i][0]['CANCELED_CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$i][0]['CANCELED_CLAIM_PARTNER_AMOUNT'],

                'PAID_NBILL' => $paid[$i][0]['PAID_NBILL'],
                'PAID_NMONTH' => $paid[$i][0]['PAID_NMONTH'],
                'PAID_FEE' => $paid[$i][0]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $paid[$i][0]['PAID_FEE_ADMIN'],
                'PAID_FEE_ADMIN_AMOUNT' => $paid[$i][0]['PAID_FEE_ADMIN_AMOUNT'],
                'PAID_FEE_VSI' => $paid[$i][0]['PAID_FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $paid[$i][0]['PAID_FEE_VSI_AMOUNT'],
                'PAID_TOTAL_FEE' => $paid[$i][0]['PAID_TOTAL_FEE'],
                'PAID_FEE_BILLER' => $paid[$i][0]['PAID_FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $paid[$i][0]['PAID_FEE_BILLER_AMOUNT'],
                'PAID_CLAIM_VSI' => $paid[$i][0]['PAID_CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $paid[$i][0]['PAID_CLAIM_VSI_AMOUNT'],
                'PAID_BILLER' => $paid[$i][0]['PAID_BILLER_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $paid[$i][0]['PAID_CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$i][0]['PAID_CLAIM_PARTNER_AMOUNT'],
            ];

            // Trx
            $tnbil = $field[$i]['TRX_NBILL'];
            $tnmonth = $field[$i]['TRX_NMONTH'];
            $tfee = $field[$i]['TRX_FEE'];
            $tFeeAdminAmount = $field[$i]['TRX_FEE_ADMIN_AMOUNT'];
            $tFeeVsiAmount = $field[$i]['TRX_FEE_VSI_AMOUNT'];
            $tTotalFee = $field[$i]['TRX_TOTAL_FEE'];
            $tFeeBillerAmount = $field[$i]['TRX_FEE_BILLER_AMOUNT'];
            $tClaimVsiAmount = $field[$i]['TRX_CLAIM_VSI_AMOUNT'];
            $tBiller = $field[$i]['TRX_BILLER'];
            $tClaimPartnerAmount = $field[$i]['TRX_CLAIM_PARTNER_AMOUNT'];

            // Paid
            $pnbil = $field[$i]['PAID_NBILL'];
            $pnmonth = $field[$i]['PAID_NMONTH'];
            $pfee = $field[$i]['PAID_FEE'];
            $pFeeAdminAmount = $field[$i]['PAID_FEE_ADMIN_AMOUNT'];
            $pFeeVsiAmount = $field[$i]['PAID_FEE_VSI_AMOUNT'];
            $pTotalFee = $field[$i]['PAID_TOTAL_FEE'];
            $pFeeBillerAmount = $field[$i]['PAID_FEE_BILLER_AMOUNT'];
            $pClaimVsiAmount = $field[$i]['PAID_CLAIM_VSI_AMOUNT'];
            $pBiller = $field[$i]['PAID_BILLER'];
            $pClaimPartnerAmount = $field[$i]['PAID_CLAIM_PARTNER_AMOUNT'];

            // Canceled
            $cnbil = $field[$i]['CANCELED_NBILL'];
            $cnmonth = $field[$i]['CANCELED_NMONTH'];
            $cfee = $field[$i]['CANCELED_FEE'];
            $cFeeAdminAmount = $field[$i]['CANCELED_FEE_ADMIN_AMOUNT'];
            $cFeeVsiAmount = $field[$i]['CANCELED_FEE_VSI_AMOUNT'];
            $cTotalFee = $field[$i]['CANCELED_TOTAL_FEE'];
            $cFeeBillerAmount = $field[$i]['CANCELED_FEE_BILLER_AMOUNT'];
            $cClaimVsiAmount = $field[$i]['CANCELED_CLAIM_VSI_AMOUNT'];
            $cBiller = $field[$i]['CANCELED_BILLER'];
            $cClaimPartnerAmount = $field[$i]['CANCELED_CLAIM_PARTNER_AMOUNT'];

            $dataTotal = [
                'nama_field' => [
                    'TOTAL_NBILL',
                    'TOTAL_NMONTH',
                    'TOTAL_FEE',
                    'TOTAL_FEE_ADMIN_AMOUNT',
                    'TOTAL_FEE_VSI_AMOUNT',
                    'TOTAL_TOTAL_FEE',
                    'TOTAL_FEE_BILLER_AMOUNT',
                    'TOTAL_CLAIM_VSI_AMOUNT',
                    'TOTAL_BILLER_AMOUNT',
                    'TOTAL_CLAIM_PARTNER_AMOUNT',
                ],
                'value_field' => [
                    $tnbil + $pnbil - $cnbil,
                    $tnmonth + $pnmonth - $cnmonth,
                    $tfee + $pfee - $cfee,
                    $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                    $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                    $tTotalFee + $pTotalFee - $cTotalFee,
                    $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                    $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                    $tBiller + $pBiller - $cBiller,
                    $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,
                ],
                'jumlah_field' => 10,
            ];

            // Mapping Field Total
            $this->mappingByProduct($data[$i], $dataPaid);
            $this->mappingByProduct($data[$i], $dataCanceled);
            $this->mappingByProduct($data[$i], $dataTotal);
        }
        // *** End Of Logic Mapping Field ***

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Clearing Variable yang tidak di return
        $paid = null;
        $canceled = null;
        $dataPaid = null;
        $dataCanceled = null;
        $dataTotal = null;

        // Response Sukses
        if (null != $countPaid || null != $countCanceled) {
            return $this->generalDataResponse(
                200,
                'Get List by Product Recon Data Success',
                $data
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get List by Product Recon Data Failed'
            );
        }
    }

    public function listByCid(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(
                [
                    'product' => ['required', 'string', 'max:100'],
                    'cid' => ['required', 'string', 'max:7'],
                    'interval_date' => ['required', 'array', 'min:1', 'max:2'],
                    'interval_date.*' => ['date_format:Y-m-d'],
                    'status' => ['required', 'numeric', 'digits:1'],
                    'items' => ['numeric', 'digits_between:1,8'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                '400',
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $product = $request->product;
        $status = $request->status;
        $idCid = $request->cid;
        $date = $request->interval_date;
        $items = (null == $request->items) ? 10 : $request->items;
        $cekPaid = [];
        $cekCancel = [];
        $paid = [];
        $canceled = [];

        // Cek Product
        $cekProduct = TransactionDefinitionV2::searchData($product)->first('CSC_TD_NAME');
        if (null == $cekProduct) {
            return $this->generalResponse(
                404,
                'Data Product/Area Not Found'
            );
        }

        // Cek CID
        $cekCid = CoreDownCentral::searchData($idCid)->first();
        if (null == $cekCid) :
            return $this->generalResponse(
                404,
                'Data CID Not Found'
            );
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
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE+CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDT_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSC_RDT_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->product($product)
            ->status($status)
            ->cid($idCid)
            ->dateRange($date)
            ->groupBy('TRX_DT')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get List by CID Recon Data Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $countData = count($data);

        // Inisialisasi Null Paid
        $nullPaid = [
            [
                'PAID_NBILL' => 0,
                'PAID_NMONTH' => 0,
                'PAID_FEE' => 0,
                'PAID_FEE_ADMIN' => 0,
                'PAID_FEE_ADMIN_AMOUNT' => 0,
                'PAID_FEE_VSI' => 0,
                'PAID_FEE_VSI_AMOUNT' => 0,
                'PAID_TOTAL_FEE' => 0,
                'PAID_FORMULA_TRANSFER' => null,
                'PAID_FEE_BILLER' => 0,
                'PAID_FEE_BILLER_AMOUNT' => 0,
                'PAID_CLAIM_VSI' => 0,
                'PAID_CLAIM_VSI_AMOUNT' => 0,
                'PAID_BILLER_AMOUNT' => 0,
                'PAID_CLAIM_PARTNER' => 0,
                'PAID_CLAIM_PARTNER_AMOUNT' => 0,
            ]
        ];

        // Inisialisasi Null Cancell
        $nullCanceled = [
            [
                'CANCELED_NBILL' => 0,
                'CANCELED_NMONTH' => 0,
                'CANCELED_FEE' => 0,
                'CANCELED_FEE_ADMIN' => 0,
                'CANCELED_FEE_ADMIN_AMOUNT' => 0,
                'CANCELED_FEE_VSI' => 0,
                'CANCELED_FEE_VSI_AMOUNT' => 0,
                'CANCELED_TOTAL_FEE' => 0,
                'CANCELED_FORMULA_TRANSFER' => null,
                'CANCELED_FEE_BILLER' => 0,
                'CANCELED_FEE_BILLER_AMOUNT' => 0,
                'CANCELED_CLAIM_VSI' => 0,
                'CANCELED_CLAIM_VSI_AMOUNT' => 0,
                'CANCELED_BILLER_AMOUNT' => 0,
                'CANCELED_CLAIM_PARTNER' => 0,
                'CANCELED_CLAIM_PARTNER_AMOUNT' => 0,
            ]
        ];

        // Logic Get Data Paid And Canceled
        for ($i=0; $i < $countData; $i++) {
            // Get Data TRX DT
            $date[$i] = $data[$i]->TRX_DT;

            // Logic Get Data Paid
            $cekPaid[$i] = TrxCorrection::select(
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS PAID_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS PAID_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS PAID_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS PAID_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS PAID_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS PAID_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS PAID_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS PAID_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS PAID_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS PAID_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS PAID_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS PAID_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS PAID_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS PAID_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->whereNull('CSM_TC_RECON_ID')
            ->where('CSM_TC_PRODUCT', $product)
            ->where('CSM_TC_CID', $idCid)
            ->where('CSM_TC_TRX_DT', $date[$i])
            ->where('CSM_TC_STATUS_TRX', 0)
            ->groupBy('CSM_TC_TRX_DT')
            ->get();

            // Handle Data Null Paid
            if (count($cekPaid[$i]) == 0) :
                $paid[$i] = $nullPaid;
            else :
                $paid[$i] = $cekPaid[$i];
            endif;

            // Logic Get Data Canceled
            $cekCancel[$i] = TrxCorrection::select(
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS CANCELED_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CANCELED_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CANCELED_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CANCELED_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CANCELED_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS CANCELED_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS CANCELED_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS CANCELED_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS CANCELED_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CANCELED_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CANCELED_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CANCELED_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CANCELED_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CANCELED_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->where('CSM_TC_PRODUCT', $product)
            ->where('CSM_TC_CID', $idCid)
            ->where('CSM_TC_TRX_DT', $date[$i])
            ->where('CSM_TC_STATUS_TRX', 1)
            ->groupBy('CSM_TC_TRX_DT')
            ->get();

            // Handle Data Null Canceled
            if (count($cekCancel[$i]) == 0) :
                $canceled[$i] = $nullCanceled;
            else :
                $canceled[$i] = $cekCancel[$i];
            endif;
        }

        // Hitung Data Paid
        $countPaid = count($paid);

        // Hitung Data Canceled
        $countCanceled = count($canceled);

        // Response Not Found
        if (null == $countData) {
            return $this->generalResponse(
                404,
                'Data List by CID Recon Data Not Found'
            );
        }

        // *** Logic Mapping Field ***
        for ($i=0; $i < $countData; $i++) {
            $data[$i] = collect($data[$i]);

            // Removing Field ID
            $data[$i]->forget('ID');

            // Data Paid Untuk Function Maping
            $dataPaid = [
                'nama_field' => [
                    'PAID_NBILL',
                    'PAID_NMONTH',
                    'PAID_FEE',
                    'PAID_FEE_ADMIN',
                    'PAID_FEE_ADMIN_AMOUNT',
                    'PAID_FEE_VSI',
                    'PAID_FEE_VSI_AMOUNT',
                    'PAID_TOTAL_FEE',
                    'PAID_FORMULA_TRANSFER',
                    'PAID_FEE_BILLER',
                    'PAID_FEE_BILLER_AMOUNT',
                    'PAID_CLAIM_VSI',
                    'PAID_CLAIM_VSI_AMOUNT',
                    'PAID_BILLER_AMOUNT',
                    'PAID_CLAIM_PARTNER',
                    'PAID_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $paid[$i][0]['PAID_NBILL'],
                    $paid[$i][0]['PAID_NMONTH'],
                    $paid[$i][0]['PAID_FEE'],
                    $paid[$i][0]['PAID_FEE_ADMIN'],
                    $paid[$i][0]['PAID_FEE_ADMIN_AMOUNT'],
                    $paid[$i][0]['PAID_FEE_VSI'],
                    $paid[$i][0]['PAID_FEE_VSI_AMOUNT'],
                    $paid[$i][0]['PAID_TOTAL_FEE'],
                    $paid[$i][0]['PAID_FORMULA_TRANSFER'],
                    $paid[$i][0]['PAID_FEE_BILLER'],
                    $paid[$i][0]['PAID_FEE_BILLER_AMOUNT'],
                    $paid[$i][0]['PAID_CLAIM_VSI'],
                    $paid[$i][0]['PAID_CLAIM_VSI_AMOUNT'],
                    $paid[$i][0]['PAID_BILLER_AMOUNT'],
                    $paid[$i][0]['PAID_CLAIM_PARTNER'],
                    $paid[$i][0]['PAID_CLAIM_PARTNER_AMOUNT'],
                ],

                'jumlah_field' => 16,
            ];

            // Data Canceled Untuk Function Maping
            $dataCanceled = [
                'nama_field' => [
                    'CANCELED_NBILL',
                    'CANCELED_NMONTH',
                    'CANCELED_FEE',
                    'CANCELED_FEE_ADMIN',
                    'CANCELED_FEE_ADMIN_AMOUNT',
                    'CANCELED_FEE_VSI',
                    'CANCELED_FEE_VSI_AMOUNT',
                    'CANCELED_TOTAL_FEE',
                    'CANCELED_FORMULA_TRANSFER',
                    'CANCELED_FEE_BILLER',
                    'CANCELED_FEE_BILLER_AMOUNT',
                    'CANCELED_CLAIM_VSI',
                    'CANCELED_CLAIM_VSI_AMOUNT',
                    'CANCELED_BILLER_AMOUNT',
                    'CANCELED_CLAIM_PARTNER',
                    'CANCELED_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $canceled[$i][0]['CANCELED_NBILL'],
                    $canceled[$i][0]['CANCELED_NMONTH'],
                    $canceled[$i][0]['CANCELED_FEE'],
                    $canceled[$i][0]['CANCELED_FEE_ADMIN'],
                    $canceled[$i][0]['CANCELED_FEE_ADMIN_AMOUNT'],
                    $canceled[$i][0]['CANCELED_FEE_VSI'],
                    $canceled[$i][0]['CANCELED_FEE_VSI_AMOUNT'],
                    $canceled[$i][0]['CANCELED_TOTAL_FEE'],
                    $canceled[$i][0]['CANCELED_FORMULA_TRANSFER'],
                    $canceled[$i][0]['CANCELED_FEE_BILLER'],
                    $canceled[$i][0]['CANCELED_FEE_BILLER_AMOUNT'],
                    $canceled[$i][0]['CANCELED_CLAIM_VSI'],
                    $canceled[$i][0]['CANCELED_CLAIM_VSI_AMOUNT'],
                    $canceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                    $canceled[$i][0]['CANCELED_CLAIM_PARTNER'],
                    $canceled[$i][0]['CANCELED_CLAIM_PARTNER_AMOUNT'],
                ],

                'jumlah_field' => 16,
            ];

            // Data Total Untuk Function Maping
            $field[$i] = [
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

                'CANCELED_NBILL' => $canceled[$i][0]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $canceled[$i][0]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $canceled[$i][0]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$i][0]['CANCELED_FEE_ADMIN'],
                'CANCELED_FEE_ADMIN_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_ADMIN_AMOUNT'],
                'CANCELED_FEE_VSI' => $canceled[$i][0]['CANCELED_FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_VSI_AMOUNT'],
                'CANCELED_TOTAL_FEE' => $canceled[$i][0]['CANCELED_TOTAL_FEE'],
                'CANCELED_FEE_BILLER' => $canceled[$i][0]['CANCELED_FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_BILLER_AMOUNT'],
                'CANCELED_CLAIM_VSI' => $canceled[$i][0]['CANCELED_CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$i][0]['CANCELED_CLAIM_VSI_AMOUNT'],
                'CANCELED_BILLER' => $canceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $canceled[$i][0]['CANCELED_CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$i][0]['CANCELED_CLAIM_PARTNER_AMOUNT'],

                'PAID_NBILL' => $paid[$i][0]['PAID_NBILL'],
                'PAID_NMONTH' => $paid[$i][0]['PAID_NMONTH'],
                'PAID_FEE' => $paid[$i][0]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $paid[$i][0]['PAID_FEE_ADMIN'],
                'PAID_FEE_ADMIN_AMOUNT' => $paid[$i][0]['PAID_FEE_ADMIN_AMOUNT'],
                'PAID_FEE_VSI' => $paid[$i][0]['PAID_FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $paid[$i][0]['PAID_FEE_VSI_AMOUNT'],
                'PAID_TOTAL_FEE' => $paid[$i][0]['PAID_TOTAL_FEE'],
                'PAID_FEE_BILLER' => $paid[$i][0]['PAID_FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $paid[$i][0]['PAID_FEE_BILLER_AMOUNT'],
                'PAID_CLAIM_VSI' => $paid[$i][0]['PAID_CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $paid[$i][0]['PAID_CLAIM_VSI_AMOUNT'],
                'PAID_BILLER' => $paid[$i][0]['PAID_BILLER_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $paid[$i][0]['PAID_CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$i][0]['PAID_CLAIM_PARTNER_AMOUNT'],
            ];

            // Trx
            $tnbil = $field[$i]['TRX_NBILL'];
            $tnmonth = $field[$i]['TRX_NMONTH'];
            $tfee = $field[$i]['TRX_FEE'];
            $tFeeAdminAmount = $field[$i]['TRX_FEE_ADMIN_AMOUNT'];
            $tFeeVsiAmount = $field[$i]['TRX_FEE_VSI_AMOUNT'];
            $tTotalFee = $field[$i]['TRX_TOTAL_FEE'];
            $tFeeBillerAmount = $field[$i]['TRX_FEE_BILLER_AMOUNT'];
            $tClaimVsiAmount = $field[$i]['TRX_CLAIM_VSI_AMOUNT'];
            $tBiller = $field[$i]['TRX_BILLER'];
            $tClaimPartnerAmount = $field[$i]['TRX_CLAIM_PARTNER_AMOUNT'];

            // Paid
            $pnbil = $field[$i]['PAID_NBILL'];
            $pnmonth = $field[$i]['PAID_NMONTH'];
            $pfee = $field[$i]['PAID_FEE'];
            $pFeeAdminAmount = $field[$i]['PAID_FEE_ADMIN_AMOUNT'];
            $pFeeVsiAmount = $field[$i]['PAID_FEE_VSI_AMOUNT'];
            $pTotalFee = $field[$i]['PAID_TOTAL_FEE'];
            $pFeeBillerAmount = $field[$i]['PAID_FEE_BILLER_AMOUNT'];
            $pClaimVsiAmount = $field[$i]['PAID_CLAIM_VSI_AMOUNT'];
            $pBiller = $field[$i]['PAID_BILLER'];
            $pClaimPartnerAmount = $field[$i]['PAID_CLAIM_PARTNER_AMOUNT'];

            // Canceled
            $cnbil = $field[$i]['CANCELED_NBILL'];
            $cnmonth = $field[$i]['CANCELED_NMONTH'];
            $cfee = $field[$i]['CANCELED_FEE'];
            $cFeeAdminAmount = $field[$i]['CANCELED_FEE_ADMIN_AMOUNT'];
            $cFeeVsiAmount = $field[$i]['CANCELED_FEE_VSI_AMOUNT'];
            $cTotalFee = $field[$i]['CANCELED_TOTAL_FEE'];
            $cFeeBillerAmount = $field[$i]['CANCELED_FEE_BILLER_AMOUNT'];
            $cClaimVsiAmount = $field[$i]['CANCELED_CLAIM_VSI_AMOUNT'];
            $cBiller = $field[$i]['CANCELED_BILLER'];
            $cClaimPartnerAmount = $field[$i]['CANCELED_CLAIM_PARTNER_AMOUNT'];

            $dataTotal = [
                'nama_field' => [
                    'TOTAL_NBILL',
                    'TOTAL_NMONTH',
                    'TOTAL_FEE',
                    'TOTAL_FEE_ADMIN_AMOUNT',
                    'TOTAL_FEE_VSI_AMOUNT',
                    'TOTAL_TOTAL_FEE',
                    'TOTAL_FEE_BILLER_AMOUNT',
                    'TOTAL_CLAIM_VSI_AMOUNT',
                    'TOTAL_BILLER_AMOUNT',
                    'TOTAL_CLAIM_PARTNER_AMOUNT',
                ],
                'value_field' => [
                    $tnbil + $pnbil - $cnbil,
                    $tnmonth + $pnmonth - $cnmonth,
                    $tfee + $pfee - $cfee,
                    $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                    $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                    $tTotalFee + $pTotalFee - $cTotalFee,
                    $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                    $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                    $tBiller + $pBiller - $cBiller,
                    $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,
                ],
                'jumlah_field' => 10,
            ];

            // Mapping Field Total
            $this->mappingByProduct($data[$i], $dataPaid);
            $this->mappingByProduct($data[$i], $dataCanceled);
            $this->mappingByProduct($data[$i], $dataTotal);
        }
        // *** End Of Logic Mapping Field ***

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Clearing Variable yang tidak di return
        $paid = null;
        $nullPaid = null;
        $canceled = null;
        $nullCanceled = null;
        $dataTotal = null;

        // Response Sukses
        if (null != $countPaid || null != $countCanceled) {
            return $this->generalDataResponse(
                200,
                'Get List by CID Recon Data Success',
                $data
            );
        }

        // Response Failed
        if (!$data) {
            return $this->generalResponse(
                500,
                'Get List by CID Recon Data Failed'
            );
        }
    }

    public function history(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate([
                'product' => ['required', 'string', 'max:100'],
                'cid' => ['required', 'string', 'max:7'],
                'trx_date' => ['required', 'date_format:Y-m-d'],
                'status' => ['required', 'numeric', 'digits:1'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->InvalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $product = $request->product;
        $status = $request->status;
        $cid = $request->cid;
        $date = $request->trx_date;
        $items = (null == $request->items) ? 10 : $request->items;

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
                DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_FEE+CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS TRX_FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_DC_ID',
                '=',
                'CSC_RDT_CID'
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSC_RDT_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->product($product)
            ->status($status)
            ->cid($cid)
            ->date($date)
            ->get();
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get Latest History Recon Data Failed', $th->getMessage());
        }

        // Hitung Data Latest
        $countLatest = count($latest);

        // Response Data Latest Not Found
        if (null == $latest[0]['PRODUCT']) :
            return $this->responseNotFound('Data List History Recon Data Not Found');
        endif;

        // *** Logic Get Data Paid And Canceled Latest ***
        for ($i=0; $i < $countLatest; $i++) {
            // Logic Get Data Paid
            $latestPaid[$i] = TrxCorrection::select(
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS PAID_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS PAID_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS PAID_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS PAID_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS PAID_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS PAID_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS PAID_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS PAID_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS PAID_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS PAID_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS PAID_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS PAID_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS PAID_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS PAID_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->product($product)
            ->cid($cid)
            ->date($date)
            ->status(0)
            ->get();

            // Handler Null Paid Nbill
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

            // Handler Null Paid Fee Admin Amount
            if (null == $latestPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT) :
                $latestPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT = 0;
            endif;

            // Handler Null Paid Fee Vsi
            if (null == $latestPaid[$i][0]->PAID_FEE_VSI) :
                $latestPaid[$i][0]->PAID_FEE_VSI = 0;
            endif;

            // Handler Null Paid Fee Vsi Amount
            if (null == $latestPaid[$i][0]->PAID_FEE_VSI_AMOUNT) :
                $latestPaid[$i][0]->PAID_FEE_VSI_AMOUNT = 0;
            endif;

            // Handler Null Paid Total Fee
            if (null == $latestPaid[$i][0]->PAID_TOTAL_FEE) :
                $latestPaid[$i][0]->PAID_TOTAL_FEE = 0;
            endif;

            // Handler Null Paid Fee Biller
            if (null == $latestPaid[$i][0]->PAID_FEE_BILLER) :
                $latestPaid[$i][0]->PAID_FEE_BILLER = 0;
            endif;

            // Handler Null Paid Fee Biller Amount
            if (null == $latestPaid[$i][0]->PAID_FEE_BILLER_AMOUNT) :
                $latestPaid[$i][0]->PAID_FEE_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Paid Claim VSI
            if (null == $latestPaid[$i][0]->PAID_CLAIM_VSI) :
                $latestPaid[$i][0]->PAID_CLAIM_VSI = 0;
            endif;

            // Handler Null Paid Claim VSI Amount
            if (null == $latestPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT) :
                $latestPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT = 0;
            endif;

            // Handler Null Paid Biller Amount
            if (null == $latestPaid[$i][0]->PAID_BILLER_AMOUNT) :
                $latestPaid[$i][0]->PAID_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Paid Claim Partner
            if (null == $latestPaid[$i][0]->PAID_CLAIM_PARTNER) :
                $latestPaid[$i][0]->PAID_CLAIM_PARTNER = 0;
            endif;

            // Handler Null Paid Claim Partner Amount
            if (null == $latestPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT) :
                $latestPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT = 0;
            endif;

            // Logic Get Data Canceled
            $latestCanceled[$i] = TrxCorrection::select(
                DB::raw('SUM(CSM_TC_NBILL) AS CANCELED_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CANCELED_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CANCELED_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CANCELED_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CANCELED_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS CANCELED_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS CANCELED_FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS CANCELED_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS CANCELED_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CANCELED_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CANCELED_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CANCELED_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CANCELED_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CANCELED_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->product($product)
            ->cid($cid)
            ->date($date)
            ->status(1)
            ->get();

            // Handler Null Canceled Nbill
            if (null == $latestCanceled[$i][0]->CANCELED_NBILL) :
                $latestCanceled[$i][0]->CANCELED_NBILL = 0;
            endif;

            // Handler Null Canceled Nmonth
            if (null == $latestCanceled[$i][0]->CANCELED_NMONTH) :
                $latestCanceled[$i][0]->CANCELED_NMONTH = 0;
            endif;

            // Handler Null Canceled Fee
            if (null == $latestCanceled[$i][0]->CANCELED_FEE) :
                $latestCanceled[$i][0]->CANCELED_FEE = 0;
            endif;

            // Handler Null Canceled Fee Admin
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_ADMIN) :
                $latestCanceled[$i][0]->CANCELED_FEE_ADMIN = 0;
            endif;

            // Handler Null Canceled Fee Admin Amount
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_ADMIN_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_FEE_ADMIN_AMOUNT = 0;
            endif;

            // Handler Null Canceled Fee Vsi
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_VSI) :
                $latestCanceled[$i][0]->CANCELED_FEE_VSI = 0;
            endif;

            // Handler Null Canceled Fee Vsi Amount
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_VSI_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_FEE_VSI_AMOUNT = 0;
            endif;

            // Handler Null Canceled Total Fee
            if (null == $latestCanceled[$i][0]->CANCELED_TOTAL_FEE) :
                $latestCanceled[$i][0]->CANCELED_TOTAL_FEE = 0;
            endif;

            // Handler Null Canceled Fee Biller
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_BILLER) :
                $latestCanceled[$i][0]->CANCELED_FEE_BILLER = 0;
            endif;

            // Handler Null Canceled Fee Biller Amount
            if (null == $latestCanceled[$i][0]->CANCELED_FEE_BILLER_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_FEE_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Canceled Claim Vsi
            if (null == $latestCanceled[$i][0]->CANCELED_CLAIM_VSI) :
                $latestCanceled[$i][0]->CANCELED_CLAIM_VSI = 0;
            endif;

            // Handler Null Canceled Claim Vsi Amount
            if (null == $latestCanceled[$i][0]->CANCELED_CLAIM_VSI_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_CLAIM_VSI_AMOUNT = 0;
            endif;

            // Handler Null Canceled Biller Amount
            if (null == $latestCanceled[$i][0]->CANCELED_BILLER_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Canceled Claim Partner
            if (null == $latestCanceled[$i][0]->CANCELED_CLAIM_PARTNER) :
                $latestCanceled[$i][0]->CANCELED_CLAIM_PARTNER = 0;
            endif;

            // Handler Null Canceled Claim Partner Amount
            if (null == $latestCanceled[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT) :
                $latestCanceled[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT = 0;
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
                    'PAID_FEE_ADMIN_AMOUNT',
                    'PAID_FEE_VSI',
                    'PAID_FEE_VSI_AMOUNT',
                    'PAID_TOTAL_FEE',
                    'PAID_FORMULA_TRANSFER',
                    'PAID_FEE_BILLER',
                    'PAID_FEE_BILLER_AMOUNT',
                    'PAID_CLAIM_VSI',
                    'PAID_CLAIM_VSI_AMOUNT',
                    'PAID_BILLER_AMOUNT',
                    'PAID_CLAIM_PARTNER',
                    'PAID_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $latestPaid[$i][0]->PAID_NBILL,
                    $latestPaid[$i][0]->PAID_NMONTH,
                    $latestPaid[$i][0]->PAID_FEE,
                    $latestPaid[$i][0]->PAID_FEE_ADMIN,
                    $latestPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT,
                    $latestPaid[$i][0]->PAID_FEE_VSI,
                    $latestPaid[$i][0]->PAID_FEE_VSI_AMOUNT,
                    $latestPaid[$i][0]->PAID_TOTAL_FEE,
                    $latestPaid[$i][0]->PAID_FORMULA_TRANSFER,
                    $latestPaid[$i][0]->PAID_FEE_BILLER,
                    $latestPaid[$i][0]->PAID_FEE_BILLER_AMOUNT,
                    $latestPaid[$i][0]->PAID_CLAIM_VSI,
                    $latestPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT,
                    $latestPaid[$i][0]->PAID_BILLER_AMOUNT,
                    $latestPaid[$i][0]->PAID_CLAIM_PARTNER,
                    $latestPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT,
                ],

                'jumlah_field' => 16,
            ];

            // Data Canceled Untuk Function Maping
            $dataLatestCanceled = [
                'nama_field' => [
                    'CANCELED_NBILL',
                    'CANCELED_NMONTH',
                    'CANCELED_FEE',
                    'CANCELED_FEE_ADMIN',
                    'CANCELED_FEE_ADMIN_AMOUNT',
                    'CANCELED_FEE_VSI',
                    'CANCELED_FEE_VSI_AMOUNT',
                    'CANCELED_TOTAL_FEE',
                    'CANCELED_FORMULA_TRANSFER',
                    'CANCELED_FEE_BILLER',
                    'CANCELED_FEE_BILLER_AMOUNT',
                    'CANCELED_CLAIM_VSI',
                    'CANCELED_CLAIM_VSI_AMOUNT',
                    'CANCELED_BILLER_AMOUNT',
                    'CANCELED_CLAIM_PARTNER',
                    'CANCELED_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $latestCanceled[$i][0]->CANCELED_NBILL,
                    $latestCanceled[$i][0]->CANCELED_NMONTH,
                    $latestCanceled[$i][0]->CANCELED_FEE,
                    $latestCanceled[$i][0]->CANCELED_FEE_ADMIN,
                    $latestCanceled[$i][0]->CANCELED_FEE_ADMIN_AMOUNT,
                    $latestCanceled[$i][0]->CANCELED_FEE_VSI,
                    $latestCanceled[$i][0]->CANCELED_FEE_VSI_AMOUNT,
                    $latestCanceled[$i][0]->CANCELED_TOTAL_FEE,
                    $latestCanceled[$i][0]->CANCELED_FORMULA_TRANSFER,
                    $latestCanceled[$i][0]->CANCELED_FEE_BILLER,
                    $latestCanceled[$i][0]->CANCELED_FEE_BILLER_AMOUNT,
                    $latestCanceled[$i][0]->CANCELED_CLAIM_VSI,
                    $latestCanceled[$i][0]->CANCELED_CLAIM_VSI_AMOUNT,
                    $latestCanceled[$i][0]->CANCELED_BILLER_AMOUNT,
                    $latestCanceled[$i][0]->CANCELED_CLAIM_PARTNER,
                    $latestCanceled[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT,
                ],

                'jumlah_field' => 16,
            ];

            // Data Total Untuk Function Maping
            $field[$i] = [
                'TRX_NBILL' => $latest[$i]['TRX_NBILL'],
                'TRX_NMONTH' => $latest[$i]['TRX_NMONTH'],
                'TRX_FEE' => $latest[$i]['TRX_FEE'],
                'TRX_FEE_ADMIN' => $latest[$i]['TRX_FEE_ADMIN'],
                'TRX_FEE_ADMIN_AMOUNT' => $latest[$i]['TRX_FEE_ADMIN_AMOUNT'],
                'TRX_FEE_VSI' => $latest[$i]['TRX_FEE_VSI'],
                'TRX_FEE_VSI_AMOUNT' => $latest[$i]['TRX_FEE_VSI_AMOUNT'],
                'TRX_TOTAL_FEE' => $latest[$i]['TRX_TOTAL_FEE'],
                'TRX_FEE_BILLER' => $latest[$i]['TRX_FEE_BILLER'],
                'TRX_FEE_BILLER_AMOUNT' => $latest[$i]['TRX_FEE_BILLER_AMOUNT'],
                'TRX_CLAIM_VSI' => $latest[$i]['TRX_CLAIM_VSI'],
                'TRX_CLAIM_VSI_AMOUNT' => $latest[$i]['TRX_CLAIM_VSI_AMOUNT'],
                'TRX_BILLER' => $latest[$i]['TRX_BILLER_AMOUNT'],
                'TRX_CLAIM_PARTNER' => $latest[$i]['TRX_CLAIM_PARTNER'],
                'TRX_CLAIM_PARTNER_AMOUNT' => $latest[$i]['TRX_CLAIM_PARTNER_AMOUNT'],

                'CANCELED_NBILL' => $latestCanceled[$i][0]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $latestCanceled[$i][0]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $latestCanceled[$i][0]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $latestCanceled[$i][0]['CANCELED_FEE_ADMIN'],
                'CANCELED_FEE_ADMIN_AMOUNT' => $latestCanceled[$i][0]['CANCELED_FEE_ADMIN_AMOUNT'],
                'CANCELED_FEE_VSI' => $latestCanceled[$i][0]['CANCELED_FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $latestCanceled[$i][0]['CANCELED_FEE_VSI_AMOUNT'],
                'CANCELED_TOTAL_FEE' => $latestCanceled[$i][0]['CANCELED_TOTAL_FEE'],
                'CANCELED_FEE_BILLER' => $latestCanceled[$i][0]['CANCELED_FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $latestCanceled[$i][0]['CANCELED_FEE_BILLER_AMOUNT'],
                'CANCELED_CLAIM_VSI' => $latestCanceled[$i][0]['CANCELED_CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $latestCanceled[$i][0]['CANCELED_CLAIM_VSI_AMOUNT'],
                'CANCELED_BILLER' => $latestCanceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $latestCanceled[$i][0]['CANCELED_CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $latestCanceled[$i][0]['CANCELED_CLAIM_PARTNER_AMOUNT'],

                'PAID_NBILL' => $latestPaid[$i][0]['PAID_NBILL'],
                'PAID_NMONTH' => $latestPaid[$i][0]['PAID_NMONTH'],
                'PAID_FEE' => $latestPaid[$i][0]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $latestPaid[$i][0]['PAID_FEE_ADMIN'],
                'PAID_FEE_ADMIN_AMOUNT' => $latestPaid[$i][0]['PAID_FEE_ADMIN_AMOUNT'],
                'PAID_FEE_VSI' => $latestPaid[$i][0]['PAID_FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $latestPaid[$i][0]['PAID_FEE_VSI_AMOUNT'],
                'PAID_TOTAL_FEE' => $latestPaid[$i][0]['PAID_TOTAL_FEE'],
                'PAID_FEE_BILLER' => $latestPaid[$i][0]['PAID_FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $latestPaid[$i][0]['PAID_FEE_BILLER_AMOUNT'],
                'PAID_CLAIM_VSI' => $latestPaid[$i][0]['PAID_CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $latestPaid[$i][0]['PAID_CLAIM_VSI_AMOUNT'],
                'PAID_BILLER' => $latestPaid[$i][0]['PAID_BILLER_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $latestPaid[$i][0]['PAID_CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $latestPaid[$i][0]['PAID_CLAIM_PARTNER_AMOUNT'],
            ];

            // Trx
            $tnbil = $field[$i]['TRX_NBILL'];
            $tnmonth = $field[$i]['TRX_NMONTH'];
            $tfee = $field[$i]['TRX_FEE'];
            $tFeeAdminAmount = $field[$i]['TRX_FEE_ADMIN_AMOUNT'];
            $tFeeVsiAmount = $field[$i]['TRX_FEE_VSI_AMOUNT'];
            $tTotalFee = $field[$i]['TRX_TOTAL_FEE'];
            $tFeeBillerAmount = $field[$i]['TRX_FEE_BILLER_AMOUNT'];
            $tClaimVsiAmount = $field[$i]['TRX_CLAIM_VSI_AMOUNT'];
            $tBiller = $field[$i]['TRX_BILLER'];
            $tClaimPartnerAmount = $field[$i]['TRX_CLAIM_PARTNER_AMOUNT'];

            // Paid
            $pnbil = $field[$i]['PAID_NBILL'];
            $pnmonth = $field[$i]['PAID_NMONTH'];
            $pfee = $field[$i]['PAID_FEE'];
            $pFeeAdminAmount = $field[$i]['PAID_FEE_ADMIN_AMOUNT'];
            $pFeeVsiAmount = $field[$i]['PAID_FEE_VSI_AMOUNT'];
            $pTotalFee = $field[$i]['PAID_TOTAL_FEE'];
            $pFeeBillerAmount = $field[$i]['PAID_FEE_BILLER_AMOUNT'];
            $pClaimVsiAmount = $field[$i]['PAID_CLAIM_VSI_AMOUNT'];
            $pBiller = $field[$i]['PAID_BILLER'];
            $pClaimPartnerAmount = $field[$i]['PAID_CLAIM_PARTNER_AMOUNT'];

            // Canceled
            $cnbil = $field[$i]['CANCELED_NBILL'];
            $cnmonth = $field[$i]['CANCELED_NMONTH'];
            $cfee = $field[$i]['CANCELED_FEE'];
            $cFeeAdminAmount = $field[$i]['CANCELED_FEE_ADMIN_AMOUNT'];
            $cFeeVsiAmount = $field[$i]['CANCELED_FEE_VSI_AMOUNT'];
            $cTotalFee = $field[$i]['CANCELED_TOTAL_FEE'];
            $cFeeBillerAmount = $field[$i]['CANCELED_FEE_BILLER_AMOUNT'];
            $cClaimVsiAmount = $field[$i]['CANCELED_CLAIM_VSI_AMOUNT'];
            $cBiller = $field[$i]['CANCELED_BILLER'];
            $cClaimPartnerAmount = $field[$i]['CANCELED_CLAIM_PARTNER_AMOUNT'];

            $dataTotal = [
                'nama_field' => [
                    'TOTAL_NBILL',
                    'TOTAL_NMONTH',
                    'TOTAL_FEE',
                    'TOTAL_FEE_ADMIN_AMOUNT',
                    'TOTAL_FEE_VSI_AMOUNT',
                    'TOTAL_TOTAL_FEE',
                    'TOTAL_FEE_BILLER_AMOUNT',
                    'TOTAL_CLAIM_VSI_AMOUNT',
                    'TOTAL_BILLER_AMOUNT',
                    'TOTAL_CLAIM_PARTNER_AMOUNT',
                ],
                'value_field' => [
                    $tnbil + $pnbil - $cnbil,
                    $tnmonth + $pnmonth - $cnmonth,
                    $tfee + $pfee - $cfee,
                    $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                    $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                    $tTotalFee + $pTotalFee - $cTotalFee,
                    $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                    $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                    $tBiller + $pBiller - $cBiller,
                    $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,
                ],
                'jumlah_field' => 10,
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

        // return $latest;

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
                DB::raw('SUM(CSC_RDTH_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSC_RDTH_FEE_VSI) AS TRX_FEE_VSI'),
                DB::raw('SUM(CSC_RDTH_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDTH_FEE+CSC_RDTH_FEE_ADMIN_AMOUNT) AS TRX_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                DB::raw('SUM(CSC_RDTH_FEE_BILLER) AS TRX_FEE_BILLER'),
                DB::raw('SUM(CSC_RDTH_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDTH_CLAIM_VSI) AS TRX_CLAIM_VSI'),
                DB::raw('SUM(CSC_RDTH_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSC_RDTH_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
                DB::raw('SUM(CSC_RDTH_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
                DB::raw('SUM(CSC_RDTH_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
                //'CSC_RDTH_RECON_ID AS RECON_ID',
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'CSC_RDTH_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSC_RDTH_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->leftJoin(
                'CSCCORE_TRX_CORRECTION AS COR',
                'CSC_RDTH_RECON_ID',
                '=',
                'CSM_TC_RECON_ID'
            )
            ->product($product)
            ->cid($cid)
            ->date($date)
            ->groupBy('VERSION')
            ->orderBy('VERSION', 'DESC')
            ->paginate($items);
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Get History Recon Data Failed', $th->getMessage());
        }

        // Hitung jumlah History
        $countHistory = count($data);

        // Response History Not Found
        if (null == $countHistory) :
            // Add Index Number
            $latest = $this->addIndexNumber($latest);

            // Create Paginate
            $latest = $this->createPaginate($latest, $items);
            return $this->generalDataResponse(200, 'Data List History Recon Data Success', $latest);
        endif;

        // *** Logic Get Data History TRX, Paid, Canceled ***
        for ($i=0; $i < $countHistory; $i++) {
            // Get Recon Id dari Data History
            $reconId[] = $data[$i]->RECON_ID;
            $historyCid[] = $data[$i]->CID;
            $historyProduct[] = $data[$i]->PRODUCT;

            // Menghitung Status Paid/Dilunaskan
            $cekPaid[] = TrxCorrection::select(
                DB::raw('SUM(CSM_TC_NBILL) AS PAID_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS PAID_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS PAID_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS PAID_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS PAID_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS PAID_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS PAID_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS PAID_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS PAID_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS PAID_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS PAID_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS PAID_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS PAID_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS PAID_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->join(
                'CSCCORE_RECON_DATA_HISTORY AS HISTORY',
                'HISTORY.CSC_RDTH_RECON_ID',
                '=',
                'CSM_TC_RECON_ID'
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'HISTORY.CSC_RDTH_CID',
                '=',
                'DC.CSC_DC_ID'
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

            // Handler Null Paid Fee Admin Amount
            if (null == $cekPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT) :
                $cekPaid[$i][0]->PAID_FEE_ADMIN_AMOUNT = 0;
            endif;

            // Handler Null Paid Fee Vsi
            if (null == $cekPaid[$i][0]->PAID_FEE_VSI) :
                $cekPaid[$i][0]->PAID_FEE_VSI = 0;
            endif;

            // Handler Null Paid Fee Vsi Amount
            if (null == $cekPaid[$i][0]->PAID_FEE_VSI_AMOUNT) :
                $cekPaid[$i][0]->PAID_FEE_VSI_AMOUNT = 0;
            endif;

            // Handler Null Paid Total Fee
            if (null == $cekPaid[$i][0]->PAID_TOTAL_FEE) :
                $cekPaid[$i][0]->PAID_TOTAL_FEE = 0;
            endif;

            // Handler Null Paid Fee Biller
            if (null == $cekPaid[$i][0]->PAID_FEE_BILLER) :
                $cekPaid[$i][0]->PAID_FEE_BILLER = 0;
            endif;

            // Handler Null Paid Fee Biller Amount
            if (null == $cekPaid[$i][0]->PAID_FEE_BILLER_AMOUNT) :
                $cekPaid[$i][0]->PAID_FEE_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Paid Claim VSI
            if (null == $cekPaid[$i][0]->PAID_CLAIM_VSI) :
                $cekPaid[$i][0]->PAID_CLAIM_VSI = 0;
            endif;

            // Handler Null Paid Claim VSI Amount
            if (null == $cekPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT) :
                $cekPaid[$i][0]->PAID_CLAIM_VSI_AMOUNT = 0;
            endif;

            // Handler Null Paid Biller Amount
            if (null == $cekPaid[$i][0]->PAID_BILLER_AMOUNT) :
                $cekPaid[$i][0]->PAID_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Paid Claim Partner
            if (null == $cekPaid[$i][0]->PAID_CLAIM_PARTNER) :
                $cekPaid[$i][0]->PAID_CLAIM_PARTNER = 0;
            endif;

            // Handler Null Paid Claim Partner Amount
            if (null == $cekPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT) :
                $cekPaid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT = 0;
            endif;

            // Save To Variable Paid
            $paid[$i][0] = $cekPaid[$i][0];

            // Menghitung Status Canceled/Dibatalkan
            $cekCancel[] = TrxCorrection::select(
                'HISTORY.CSC_RDTH_RECON_ID AS RECON_ID',
                'HISTORY.CSC_RDTH_PRODUCT AS PRODUCT',
                'HISTORY.CSC_RDTH_CID AS CID',
                'HISTORY.CSC_RDTH_CID AS CID_NAME',
                'HISTORY.CSC_RDTH_TRX_DT AS TRX_DT',
                'HISTORY.CSC_RDTH_VERSION AS VERSION',
                DB::raw('SUM(CSM_TC_NBILL) AS CANCELED_NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS CANCELED_NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS CANCELED_FEE'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS CANCELED_FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS CANCELED_FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS CANCELED_FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS CANCELED_TOTAL_FEE'),
                'FT.CSC_FH_FORMULA AS FORMULA_TRANSFER',
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS CANCELED_FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS CANCELED_FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CANCELED_CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CANCELED_CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS CANCELED_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CANCELED_CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CANCELED_CLAIM_PARTNER_AMOUNT'),
            )
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FT',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FT.CSC_FH_ID'
            )
            ->join(
                'CSCCORE_RECON_DATA_HISTORY AS HISTORY',
                'HISTORY.CSC_RDTH_RECON_ID',
                '=',
                'CSM_TC_RECON_ID'
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS DC',
                'HISTORY.CSC_RDTH_CID',
                '=',
                'DC.CSC_DC_ID'
            )
            ->recon($reconId[$i])
            ->status(1)
            ->orderBy('HISTORY.CSC_RDTH_VERSION', 'DESC')
            ->get();

            // Handler Null Canceled Nbill
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

            // Handler Null Canceled Fee Admin Amount
            if (null == $cekCancel[$i][0]->CANCELED_FEE_ADMIN_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_FEE_ADMIN_AMOUNT = 0;
            endif;

            // Handler Null Canceled Fee Vsi
            if (null == $cekCancel[$i][0]->CANCELED_FEE_VSI) :
                $cekCancel[$i][0]->CANCELED_FEE_VSI = 0;
            endif;

            // Handler Null Canceled Fee Vsi Amount
            if (null == $cekCancel[$i][0]->CANCELED_FEE_VSI_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_FEE_VSI_AMOUNT = 0;
            endif;

            // Handler Null Canceled Total Fee
            if (null == $cekCancel[$i][0]->CANCELED_TOTAL_FEE) :
                $cekCancel[$i][0]->CANCELED_TOTAL_FEE = 0;
            endif;

            // Handler Null Canceled Fee Biller
            if (null == $cekCancel[$i][0]->CANCELED_FEE_BILLER) :
                $cekCancel[$i][0]->CANCELED_FEE_BILLER = 0;
            endif;

            // Handler Null Canceled Fee Biller Amount
            if (null == $cekCancel[$i][0]->CANCELED_FEE_BILLER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_FEE_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Canceled Claim Vsi
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_VSI) :
                $cekCancel[$i][0]->CANCELED_CLAIM_VSI = 0;
            endif;

            // Handler Null Canceled Claim Vsi Amount
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_VSI_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_CLAIM_VSI_AMOUNT = 0;
            endif;

            // Handler Null Canceled Biller Amount
            if (null == $cekCancel[$i][0]->CANCELED_BILLER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_BILLER_AMOUNT = 0;
            endif;

            // Handler Null Canceled Claim Partner
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER) :
                $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER = 0;
            endif;

            // Handler Null Canceled Claim Partner Amount
            if (null == $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT) :
                $cekCancel[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT = 0;
            endif;

            // Save To Variable Canceled
            $canceled[$i][0] = $cekCancel[$i][0];
        }
        // *** END OF Logic Get Data Latest TRX, Paid, Canceled ***

        // *** Logic Mapping Data Field ***
        for ($i=0; $i < $countHistory; $i++) {
            $data[$i] = collect($data[$i]);

            // Data Paid Untuk Function Maping
            $dataPaid = [
                'nama_field' => [
                    'PAID_NBILL',
                    'PAID_NMONTH',
                    'PAID_FEE',
                    'PAID_FEE_ADMIN',
                    'PAID_FEE_ADMIN_AMOUNT',
                    'PAID_FEE_VSI',
                    'PAID_FEE_VSI_AMOUNT',
                    'PAID_TOTAL_FEE',
                    'PAID_FEE_BILLER',
                    'PAID_FEE_BILLER_AMOUNT',
                    'PAID_CLAIM_VSI',
                    'PAID_CLAIM_VSI_AMOUNT',
                    'PAID_BILLER_AMOUNT',
                    'PAID_CLAIM_PARTNER',
                    'PAID_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $paid[$i][0]->PAID_NBILL,
                    $paid[$i][0]->PAID_NMONTH,
                    $paid[$i][0]->PAID_FEE,
                    $paid[$i][0]->PAID_FEE_ADMIN,
                    $paid[$i][0]->PAID_FEE_ADMIN_AMOUNT,
                    $paid[$i][0]->PAID_FEE_VSI,
                    $paid[$i][0]->PAID_FEE_VSI_AMOUNT,
                    $paid[$i][0]->PAID_TOTAL_FEE,
                    $paid[$i][0]->PAID_FEE_BILLER,
                    $paid[$i][0]->PAID_FEE_BILLER_AMOUNT,
                    $paid[$i][0]->PAID_CLAIM_VSI,
                    $paid[$i][0]->PAID_CLAIM_VSI_AMOUNT,
                    $paid[$i][0]->PAID_BILLER_AMOUNT,
                    $paid[$i][0]->PAID_CLAIM_PARTNER,
                    $paid[$i][0]->PAID_CLAIM_PARTNER_AMOUNT,
                ],

                'jumlah_field' => 15,
            ];

            // Data Canceled Untuk Function Maping
            $dataCanceled = [
                'nama_field' => [
                    'CANCELED_NBILL',
                    'CANCELED_NMONTH',
                    'CANCELED_FEE',
                    'CANCELED_FEE_ADMIN',
                    'CANCELED_FEE_ADMIN_AMOUNT',
                    'CANCELED_FEE_VSI',
                    'CANCELED_FEE_VSI_AMOUNT',
                    'CANCELED_TOTAL_FEE',
                    'CANCELED_FEE_BILLER',
                    'CANCELED_FEE_BILLER_AMOUNT',
                    'CANCELED_CLAIM_VSI',
                    'CANCELED_CLAIM_VSI_AMOUNT',
                    'CANCELED_BILLER_AMOUNT',
                    'CANCELED_CLAIM_PARTNER',
                    'CANCELED_CLAIM_PARTNER_AMOUNT',
                ],

                'value_field' => [
                    $canceled[$i][0]->CANCELED_NBILL,
                    $canceled[$i][0]->CANCELED_NMONTH,
                    $canceled[$i][0]->CANCELED_FEE,
                    $canceled[$i][0]->CANCELED_FEE_ADMIN,
                    $canceled[$i][0]->CANCELED_FEE_ADMIN_AMOUNT,
                    $canceled[$i][0]->CANCELED_FEE_VSI,
                    $canceled[$i][0]->CANCELED_FEE_VSI_AMOUNT,
                    $canceled[$i][0]->CANCELED_TOTAL_FEE,
                    $canceled[$i][0]->CANCELED_FEE_BILLER,
                    $canceled[$i][0]->CANCELED_FEE_BILLER_AMOUNT,
                    $canceled[$i][0]->CANCELED_CLAIM_VSI,
                    $canceled[$i][0]->CANCELED_CLAIM_VSI_AMOUNT,
                    $canceled[$i][0]->CANCELED_BILLER_AMOUNT,
                    $canceled[$i][0]->CANCELED_CLAIM_PARTNER,
                    $canceled[$i][0]->CANCELED_CLAIM_PARTNER_AMOUNT,
                ],

                'jumlah_field' => 15,
            ];

            // Data Total Untuk Function Maping
            $field[$i] = [
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

                'CANCELED_NBILL' => $canceled[$i][0]['CANCELED_NBILL'],
                'CANCELED_NMONTH' => $canceled[$i][0]['CANCELED_NMONTH'],
                'CANCELED_FEE' => $canceled[$i][0]['CANCELED_FEE'],
                'CANCELED_FEE_ADMIN' => $canceled[$i][0]['CANCELED_FEE_ADMIN'],
                'CANCELED_FEE_ADMIN_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_ADMIN_AMOUNT'],
                'CANCELED_FEE_VSI' => $canceled[$i][0]['CANCELED_FEE_VSI'],
                'CANCELED_FEE_VSI_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_VSI_AMOUNT'],
                'CANCELED_TOTAL_FEE' => $canceled[$i][0]['CANCELED_TOTAL_FEE'],
                'CANCELED_FEE_BILLER' => $canceled[$i][0]['CANCELED_FEE_BILLER'],
                'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$i][0]['CANCELED_FEE_BILLER_AMOUNT'],
                'CANCELED_CLAIM_VSI' => $canceled[$i][0]['CANCELED_CLAIM_VSI'],
                'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$i][0]['CANCELED_CLAIM_VSI_AMOUNT'],
                'CANCELED_BILLER' => $canceled[$i][0]['CANCELED_BILLER_AMOUNT'],
                'CANCELED_CLAIM_PARTNER' => $canceled[$i][0]['CANCELED_CLAIM_PARTNER'],
                'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$i][0]['CANCELED_CLAIM_PARTNER_AMOUNT'],

                'PAID_NBILL' => $paid[$i][0]['PAID_NBILL'],
                'PAID_NMONTH' => $paid[$i][0]['PAID_NMONTH'],
                'PAID_FEE' => $paid[$i][0]['PAID_FEE'],
                'PAID_FEE_ADMIN' => $paid[$i][0]['PAID_FEE_ADMIN'],
                'PAID_FEE_ADMIN_AMOUNT' => $paid[$i][0]['PAID_FEE_ADMIN_AMOUNT'],
                'PAID_FEE_VSI' => $paid[$i][0]['PAID_FEE_VSI'],
                'PAID_FEE_VSI_AMOUNT' => $paid[$i][0]['PAID_FEE_VSI_AMOUNT'],
                'PAID_TOTAL_FEE' => $paid[$i][0]['PAID_TOTAL_FEE'],
                'PAID_FEE_BILLER' => $paid[$i][0]['PAID_FEE_BILLER'],
                'PAID_FEE_BILLER_AMOUNT' => $paid[$i][0]['PAID_FEE_BILLER_AMOUNT'],
                'PAID_CLAIM_VSI' => $paid[$i][0]['PAID_CLAIM_VSI'],
                'PAID_CLAIM_VSI_AMOUNT' => $paid[$i][0]['PAID_CLAIM_VSI_AMOUNT'],
                'PAID_BILLER' => $paid[$i][0]['PAID_BILLER_AMOUNT'],
                'PAID_CLAIM_PARTNER' => $paid[$i][0]['PAID_CLAIM_PARTNER'],
                'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$i][0]['PAID_CLAIM_PARTNER_AMOUNT'],
            ];

            // Trx
            $tnbil = $field[$i]['TRX_NBILL'];
            $tnmonth = $field[$i]['TRX_NMONTH'];
            $tfee = $field[$i]['TRX_FEE'];
            $tFeeAdminAmount = $field[$i]['TRX_FEE_ADMIN_AMOUNT'];
            $tFeeVsiAmount = $field[$i]['TRX_FEE_VSI_AMOUNT'];
            $tTotalFee = $field[$i]['TRX_TOTAL_FEE'];
            $tFeeBillerAmount = $field[$i]['TRX_FEE_BILLER_AMOUNT'];
            $tClaimVsiAmount = $field[$i]['TRX_CLAIM_VSI_AMOUNT'];
            $tBiller = $field[$i]['TRX_BILLER'];
            $tClaimPartnerAmount = $field[$i]['TRX_CLAIM_PARTNER_AMOUNT'];

            // Paid
            $pnbil = $field[$i]['PAID_NBILL'];
            $pnmonth = $field[$i]['PAID_NMONTH'];
            $pfee = $field[$i]['PAID_FEE'];
            $pFeeAdminAmount = $field[$i]['PAID_FEE_ADMIN_AMOUNT'];
            $pFeeVsiAmount = $field[$i]['PAID_FEE_VSI_AMOUNT'];
            $pTotalFee = $field[$i]['PAID_TOTAL_FEE'];
            $pFeeBillerAmount = $field[$i]['PAID_FEE_BILLER_AMOUNT'];
            $pClaimVsiAmount = $field[$i]['PAID_CLAIM_VSI_AMOUNT'];
            $pBiller = $field[$i]['PAID_BILLER'];
            $pClaimPartnerAmount = $field[$i]['PAID_CLAIM_PARTNER_AMOUNT'];

            // Canceled
            $cnbil = $field[$i]['CANCELED_NBILL'];
            $cnmonth = $field[$i]['CANCELED_NMONTH'];
            $cfee = $field[$i]['CANCELED_FEE'];
            $cFeeAdminAmount = $field[$i]['CANCELED_FEE_ADMIN_AMOUNT'];
            $cFeeVsiAmount = $field[$i]['CANCELED_FEE_VSI_AMOUNT'];
            $cTotalFee = $field[$i]['CANCELED_TOTAL_FEE'];
            $cFeeBillerAmount = $field[$i]['CANCELED_FEE_BILLER_AMOUNT'];
            $cClaimVsiAmount = $field[$i]['CANCELED_CLAIM_VSI_AMOUNT'];
            $cBiller = $field[$i]['CANCELED_BILLER'];
            $cClaimPartnerAmount = $field[$i]['CANCELED_CLAIM_PARTNER_AMOUNT'];

            $dataTotal = [
                'nama_field' => [
                    'TOTAL_NBILL',
                    'TOTAL_NMONTH',
                    'TOTAL_FEE',
                    'TOTAL_FEE_ADMIN_AMOUNT',
                    'TOTAL_FEE_VSI_AMOUNT',
                    'TOTAL_TOTAL_FEE',
                    'TOTAL_FEE_BILLER_AMOUNT',
                    'TOTAL_CLAIM_VSI_AMOUNT',
                    'TOTAL_BILLER_AMOUNT',
                    'TOTAL_CLAIM_PARTNER_AMOUNT',
                ],
                'value_field' => [
                    $tnbil + $pnbil - $cnbil,
                    $tnmonth + $pnmonth - $cnmonth,
                    $tfee + $pfee - $cfee,
                    $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                    $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                    $tTotalFee + $pTotalFee - $cTotalFee,
                    $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                    $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                    $tBiller + $pBiller - $cBiller,
                    $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,
                ],
                'jumlah_field' => 10,
            ];

            // Menghilangkan Jam Settled Dt
            $data[$i]->put('SETTLED_DT', substr($data[0]['SETTLED_DT'], 0, 10));

            // Menghapus Field Recon ID
            $data[$i]->forget('RECON_ID');

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

        // Response Failed
        if (!$data) :
            return $this->failedResponse('Data List History Recon Data Failed');
        endif;

        // Response Success
        if (false != $data) :
            // Add Index Number
            $data = $this->addIndexNumber($data);

            return $this->generalDataResponse(200, 'Data List History Recon Data Success', $data);
        endif;
    }

    public function export(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'product' => ['required', 'array', 'min:1'],
                'product.*' => ['string', 'max:100'],
                'interval_date' => ['required', 'array', 'max:2'],
                'interval_date.*' => ['date_format:Y-m-d'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $product = $request->product;
        $countProduct = count($product);
        $date = $request->interval_date;
        $notFound = [];
        $notRegistered = [];
        $checkProduct = [];

        // Check Product Not Registered
        for ($i=0; $i < $countProduct; $i++) {
            $checkProduct[] = $this->productCheckData($product[$i]);

            if (false == $checkProduct[$i]) :
                $notRegistered[] = $product[$i];
                unset($product[$i]);
            endif;
        }

        // Reordering Data Product
        $product = array_values($product);
        $countProduct = count($product);
        $checkProduct = [];

        // Check Product Found
        for ($i=0; $i < $countProduct; $i++) {
            $checkProduct[] = $this->reconDataProductDateRange($product[$i], $date);

            if (false == $checkProduct[$i]) :
                $notFound[] = $product[$i];
                unset($product[$i]);
            endif;
        }

        // Reordering Data Product
        $product = array_values($product);
        $countProduct = count($product);
        $checkProduct = [];

        // Return Response Not Found And Not registered
        if (null != $notFound || null != $notRegistered) :
            $status = 202;

            if (null != $notFound) :
                $response['product_not_found'] = $notFound;
                $message = 'Export Recon Data Success but Some Product Between '.
                'The Selected Transaction Dates Not Found';
            endif;

            if (null != $notRegistered) :
                $response['product_not_registered'] = $notRegistered;
                $message = 'Export Recon Data Success but Some Product Not Registered';
            endif;

            if (null != $notFound && null != $notRegistered) :
                $response['product_not_registered'] = $notRegistered;
                $response['product_not_found'] = $notFound;
                $message = 'Export Recon Data Success but Some Product Cannot Processed';
            endif;

            return $this->generalDataResponse($status, $message, $response);
        endif;

        // *** LOGIC GET DATA SUMMARY *** //
        $data = CoreReconData::select(
            'CSC_RDT_PRODUCT AS PRODUCT',
            DB::raw('SUM(CSC_RDT_NBILL) AS TRX_NBILL'),
            DB::raw('SUM(CSC_RDT_NMONTH) AS TRX_NMONTH'),
            DB::raw('SUM(CSC_RDT_FEE) AS TRX_FEE'),
            DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS TRX_FEE_ADMIN'),
            DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
            DB::raw('SUM(CSC_RDT_FEE_VSI) AS TRX_FEE_VSI'),
            DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS TRX_FEE_VSI_AMOUNT'),
            DB::raw('SUM(CSC_RDT_FEE+CSC_RDT_FEE_ADMIN_AMOUNT) AS TRX_TOTAL_FEE'),
            DB::raw('SUM(CSC_RDT_FEE_BILLER) AS TRX_FEE_BILLER'),
            DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS TRX_FEE_BILLER_AMOUNT'),
            DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS TRX_CLAIM_VSI'),
            DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS TRX_CLAIM_VSI_AMOUNT'),
            DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS TRX_BILLER_AMOUNT'),
            DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS TRX_CLAIM_PARTNER'),
            DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS TRX_CLAIM_PARTNER_AMOUNT'),
            'CSC_RDT_USER_SETTLED AS USER_SETTLED',
            'CSC_RDT_STATUS AS STATUS',
        )
        ->where(function ($query) use ($product, $countProduct) {
            if (1 == $countProduct) :
                $query->product($product[0]);
            endif;

            if (1 < $countProduct) :
                $query->product($product[0]);
                for ($i=1; $i < $countProduct; $i++) {
                    $query->orWhere('CSC_RDT_PRODUCT', $product[$i]);
                }
            endif;
        })
        ->dateRange($date)
        ->groupBy('CSC_RDT_PRODUCT')
        ->groupBy('CSC_RDT_STATUS')
        ->orderBy('CSC_RDT_TRX_DT', 'ASC')
        ->get();

        // Count Data
        $countData = count($data);

        // Response Not Found
        if (null == $countData) :
            return $this->reconDataNotFound();
        endif;

        // Change Status Valueproduct
        for ($i=0; $i < $countData; $i++) {
            // Initialize Status Data
            if (0 == $data[$i]['STATUS']) :
                $status = 'Settled';
            elseif (1 == $data[$i]['STATUS']) :
                $status = 'Process';
            elseif (2 == $data[$i]['STATUS']) :
                $status = 'Waiting Process';
            endif;

            // Mapping New Value Status
            $data[$i] = collect($data[$i]);
            $data[$i]->put('STATUS', $status);
        }
        // *** END OF LOGIC GET DATA SUMMARY *** //

        // Convert Json To Array
        $data = $data->toArray();

        $header =  array_keys($data[0]);
        $fileName = 'SUMMARY_DATA_PRODUCT_'. $date[0].'_'.$date[1].'.xlsx';

        // Try Download File Excel
        try {
            $parameter = [
                'date' => $date,
                'product' => $product,
                'countProduct' => $countProduct,
            ];

            // return Excel::download(
            //     new ReconDataExport($data, $header, 'SUMMARY_RECON_DATA', $parameter),
            //     $fileName
            // );

            Excel::store(
                new ReconDataExport($data, $header, 'SUMMARY_RECON_DATA', $parameter),
                $fileName,
                'xlsx_recon_data',
                ExcelExcel::XLSX
            );

            $file = base64_encode($fileName);
            $response['url'] = url('api/recon-data/export-download/'.$file);
            return $this->generalDataResponse(200, 'Export Summary Recon Data Success', $response);
        } catch (\Throwable $th) {
            return $this->generalDataResponse(
                500,
                'Export Recon Data Failed',
                $th->getMessage()
            );
        }
    }

    public function download(Request $request, $id)
    {
        // Validasi Data ID
        if (Str::length($id) > 64) :
            $id = ['Id' => 'The id must not be greater than 64 characters.'];
            return $this->invalidValidation($id);
        endif;

        // Inisialisasi Variable
        $id = base64_decode($request->id);

        // Logic Stream Data Summary
        try {
            return response()->download(storage_path(env('XLSX_RECON_DATA'). $id));
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Download Export Recon Data Failed', $th->getMessage());
        }
    }
}
