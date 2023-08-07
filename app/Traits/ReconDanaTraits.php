<?php

namespace App\Traits;

use App\Models\CoreCorrection;
use App\Models\CoreGroupTransferFunds;
use App\Models\ReconDana;
use App\Models\TrxCorrection;
use App\Traits\ResponseHandler;
use Illuminate\Support\Facades\DB;

trait ReconDanaTraits
{
    use ResponseHandler;

    // Response Data Unmapping Biller Not Found
    public function reconDanaUnmappingBillerNotFound()
    {
        return $this->responseNotFound('Data Unmapping Group Biller-Biller Not Found');
    }

    // Response Data Unmapping Product Not Found
    public function reconDanaUnmappingProductNotFound()
    {
        return $this->responseNotFound('Data Unmapping Group Transfer-Product Not Found');
    }

    // Response Data Recon Dana Not Found
    public function reconDanaNotFound()
    {
        return $this->responseNotFound('Data Recon Dana Not Found');
    }

    // Function Mapping array
    public function reconDanaMapping($data, $map)
    {
        $keys = array_keys($map);
        $values = array_values($map);
        $count = count($keys);

        for ($i=0; $i < $count; $i++) {
            $data->put($keys[$i], $values[$i]);
        }
    }

    // Search Recon Dana By Id
    public function reconDanaById($id)
    {
        // Logic Get Data
        $data = ReconDana::id($id)->first();

        // Null == false | !Null == data
        return (null != $data) ? $data : false;
    }

    // Get Biller Product
    public function reconDanaBillerProduct($biller, $date)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'CSC_RDN_ID',
            'CSC_RDN_PRODUCT',
            'CSC_RDN_STATUS',
            'CSC_RDN_SETTLED_DT',
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'CSC_RDN_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->whereNull('TD.CSC_TD_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'TD.CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNull('CSC_BILLER_DELETED_DT')
        ->where('CSC_BILLER_ID', $biller)
        ->where('CSC_RDN_SETTLED_DT', $date)
        ->where('CSC_RDN_STATUS', 1)
        // ->groupBy('CSC_RDN_PRODUCT')
        ->get();

        // Hitung Jumlah Data
        $countData = count($data);

        // null = false | !null = $data
        return (null != $countData) ? $data : false;
    }

    // Get Recon Dana By Product
    public function reconDanaProduct($product)
    {
        // Logic Get Data
        $data = ReconDana::product($product)->first();

        // null = false | !null = data
        return (null != $data) ? $data : false;
    }

    // Get Correection Settled List Berdasarkan  Correction Value
    public function reconDanaCorrectionListType($biller, $date, $type, $items)
    {
        // Logic Get data
        $data = CoreGroupTransferFunds::join(
            'CSCCORE_CORRECTION AS CORRECTION',
            'CSC_GTF_ID',
            '=',
            'CORRECTION.CSC_CORR_GROUP_TRANSFER'
        )
        ->whereNull('CSC_GTF_DELETED_DT')
        ->whereNull('CORRECTION.CSC_CORR_DELETED_DT')
        ->join(
            'CSCCORE_PRODUCT_FUNDS AS PF',
            'CSC_GTF_ID',
            '=',
            'PF.CSC_PF_GROUP_TRANSFER'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'PF.CSC_PF_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->whereNull('TD.CSC_TD_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'TD.CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'CSC_BILLER_ID'
        )
        ->whereNull('B.CSC_BILLER_DELETED_DT')
        ->where('B.CSC_BILLER_ID', $biller)
        ->where('CORRECTION.CSC_CORR_DATE_TRANSFER', $date)
        ->where('CORRECTION.CSC_CORR_CORRECTION_VALUE', $type)
        ->groupBy('CORRECTION.CSC_CORR_ID')
        ->paginate(
            $items,
            $column = [
                'B.CSC_BILLER_NAME AS BILLER',
                'CSC_GTF_NAME AS GROUP_TRANSFER',
                'CORRECTION.CSC_CORR_DATE_TRANSFER AS DATE_TRANSFER',
                'CORRECTION.CSC_CORR_CORRECTION AS AMOUNT',
                'CORRECTION.CSC_CORR_CORRECTION_VALUE AS TYPE',
                'CORRECTION.CSC_CORR_DESC AS DESC',
            ],
        );

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !null == data
        return (null != $countData) ? $data : false;
    }

    // Get Correection Settled List
    public function reconDanaCorrectionList($biller, $items)
    {
        // Logic Get data
        $data = CoreGroupTransferFunds::select(
            'CORRECTION.CSC_CORR_ID AS ID',
            'B.CSC_BILLER_NAME AS BILLER',
            'CSC_GTF_NAME AS GROUP_TRANSFER',
            'CORRECTION.CSC_CORR_DATE_TRANSFER AS DATE_TRANSFER',
            'CORRECTION.CSC_CORR_CORRECTION AS AMOUNT',
            'CORRECTION.CSC_CORR_CORRECTION_VALUE AS TYPE',
            'CORRECTION.CSC_CORR_DESC AS DESC',
        )
        ->join(
            'CSCCORE_CORRECTION AS CORRECTION',
            'CSC_GTF_ID',
            '=',
            'CORRECTION.CSC_CORR_GROUP_TRANSFER'
        )
        ->whereNull('CSC_GTF_DELETED_DT')
        ->whereNull('CORRECTION.CSC_CORR_DELETED_DT')
        ->join(
            'CSCCORE_PRODUCT_FUNDS AS PF',
            'CSC_GTF_ID',
            '=',
            'PF.CSC_PF_GROUP_TRANSFER'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'PF.CSC_PF_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->whereNull('TD.CSC_TD_DELETED_DT')
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'TD.CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'CSC_BILLER_ID'
        )
        ->whereNull('CORRECTION.CSC_CORR_DELETED_DT')
        ->whereNull('B.CSC_BILLER_DELETED_DT')
        ->whereNull('CORRECTION.CSC_CORR_RECON_DANA_ID')
        ->where('CORRECTION.CSC_CORR_STATUS', 1)
        ->where('B.CSC_BILLER_ID', $biller)
        ->groupBy('CORRECTION.CSC_CORR_ID')
        ->paginate(
            $items,
        );

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !null == data
        return (null != $countData) ? $data : false;
    }

    // Get List Suspect Process
    public function reconDanaListSuspect($biller, $items)
    {
        // Logic Get Data
        $data = TrxCorrection::select(
            'B.CSC_BILLER_NAME AS BILLER',
            'TD.CSC_TD_NAME AS PRODUCT',
            'CSM_TC_TRX_DT AS TRX_DT',
            'CSM_TC_BILLER_AMOUNT AS AMOUNT',
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'CSM_TC_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'TD.CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNull('CSM_TC_RECON_DANA_ID')
        ->statusData(0)
        ->statusFunds(1)
        ->where('B.CSC_BILLER_ID', $biller)
        ->groupBy('CSM_TC_TRX_DT')
        ->paginate($items);

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !Null == data
        return (null != $countData) ? $data : false;
    }

    // Get List Suspect Process By Status Trx
    public function reconDanaListSuspectByStatus($biller, $status)
    {
        // Logic Get Data
        $data = TrxCorrection::select(
            'CSM_TC_ID AS ID',
            DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS AMOUNT')
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'CSM_TC_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BP',
            'TD.CSC_TD_NAME',
            '=',
            'BP.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'BP.CSC_BP_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNull('CSM_TC_RECON_DANA_ID')
        ->statusData(0)
        ->statusFunds(1)
        ->status($status)
        ->where('B.CSC_BILLER_ID', $biller)
        ->groupBy('CSM_TC_TRX_DT')
        ->get();

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !Null == data
        return (null != $countData) ? $data : false;
    }

    // Get List Data Summary
    public function reconDanaListSummary($group, $date, $items)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'GB.CSC_GB_ID AS GROUP_BILLER_ID',
            'GB.CSC_GB_NAME AS GROUP_BILLER',
            'CSC_RDN_SETTLED_DT AS DATE_TRANSFER',
            'CSC_RDN_START_DT AS START_PERIOD',
            'CSC_RDN_END_DT AS END_PERIOD',
            'CSC_RDN_END_DT AS RECON_PERIOD',
            'CSC_RDN_AMOUNT_TRANSFER AS PROCESS_TOTAL',
            'CSC_RDN_AMOUNT_TRANSFER AS UNPROCESS_TOTAL',
            'CSC_RDN_AMOUNT_TRANSFER AS TOTAL',
        )
        ->listSummary()
        ->startEndDate($date)
        ->where(function ($query) use ($group) {
            if (null != $group) :
                $query->where('GB.CSC_GB_ID', $group);
            endif;
        })
        ->groupBy('CSC_RDN_SETTLED_DT')
        ->groupBy('GB.CSC_GB_NAME')
        ->paginate($items);

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !Null == data
        return (null != $countData) ? $data : false;
    }

    // Get List Data Process Summary
    public function reconDanaProcessSummary($group, $date)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'CSC_RDN_BILLER',
            DB::raw('SUM(CSC_RDN_AMOUNT_TRANSFER) AS AMOUNT_TRANSFER')
        )
        ->listSummary()
        ->settledDt($date)
        ->where('GB.CSC_GB_ID', $group)
        ->where(function ($query) {
            $query->where('CSC_RDN_STATUS', 0)
            ->orWhere('CSC_RDN_STATUS', 2)
            ->orWhere('CSC_RDN_STATUS', 3);
        })
        ->groupBy('CSC_RDN_SETTLED_DT')
        ->first();


        // Null == 0 | !Null = data
        if (null == $data) :
            return $data = ['AMOUNT_TRANSFER' => 0];
        else :
            return $data;
        endif;
    }

    // Get List Data Unprocess Summary
    public function reconDanaUnprocessSummary($group, $date)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'CSC_RDN_BILLER',
            DB::raw('SUM(CSC_RDN_AMOUNT_TRANSFER) AS AMOUNT_TRANSFER'),
        )
        ->listSummary()
        ->settledDt($date)
        ->where('GB.CSC_GB_ID', $group)
        ->where('CSC_RDN_STATUS', 1)
        ->groupBy('CSC_RDN_SETTLED_DT')
        ->groupBy('CSC_RDN_BILLER')
        ->first();

        // Null == 0 | !Null = data
        if (null == $data) :
            return $data = ['AMOUNT_TRANSFER' => 0];
        else :
            return $data;
        endif;
    }

    // Get List Data Process & Unprocess List Recon
    public function reconDanaProcessList($date, $type = null, $status = null)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'CSC_RDN_BILLER',
            DB::raw('SUM(CSC_RDN_AMOUNT_TRANSFER) AS AMOUNT_TRANSFER'),
            // 'CSC_RDN_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
            'CSC_RDN_STATUS AS STATUS'
        )
        ->summary()
        ->where(
            function ($query) use ($type, $status) {
                // Filter Type
                if (null != $type) :
                    $query->type($type);
                endif;

                // Filter Status
                if (null != $status) :
                    $query->status($status);
                endif;
            }
        )
        ->settledDt($date)
        ->groupBy('CSC_RDN_GROUP_TRANSFER')
        ->get();

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == 0 | !Null = data
        return (null != $countData) ? $data : false;
    }

    // Get Data Summary List Recon
    public function reconDanaSummary($group, $date, $type = null, $status = null)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'BANK.CSC_BANK_NAME AS SUM_ACCOUNT',
            'CSC_RDN_AMOUNT_TRANSFER AS SUM_PROCESS',
            'CSC_RDN_AMOUNT_TRANSFER AS SUM_UNPROCESS',
            'CSC_RDN_AMOUNT_TRANSFER AS SUM_TOTAL',
            'CSC_RDN_BILLER AS BILLER',
            'BANK.CSC_BANK_NAME AS BANK',
            'ACCOUNT.CSC_ACCOUNT_NUMBER AS ACCOUNT_NUMBER',
            DB::raw('SUM(CSC_RDN_AMOUNT_TRANSFER) AS AMOUNT_TRANSFER')
        )
        ->summary()
        ->where(
            function ($query) use ($type, $status) {
                // Filter Type
                if (null != $type) :
                    $query->type($type);
                endif;

                // Filter Status
                if (null != $status) :
                    $query->status($status);
                endif;
            }
        )
        ->where('GB.CSC_GB_ID', $group)
        ->settledDt($date)
        ->groupBy('CSC_RDN_GROUP_TRANSFER')
        ->get();

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !Null == data
        return (null != $countData) ? $data : false;
    }

    // Get Data List Recon
    public function reconDanaList($group, $date, $items, $type = null, $status = null)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'CSC_RDN_ID AS ID',
            'CSC_RDN_BILLER AS BILLER_ID',
            'BILLER.CSC_BILLER_NAME AS BILLER',
            'CSC_RDN_GROUP_TRANSFER AS GROUP_TARNSFER_ID',
            'GTF.CSC_GTF_NAME AS GROUP_TRANSFER',
            'CSC_RDN_START_DT AS START',
            'CSC_RDN_END_DT AS END',
            'CSC_RDN_SETTLED_DT AS DATE_TRANSFER',
            'GTF.CSC_GTF_SOURCE AS SOURCE_ACCOUNT',
            'GTF.CSC_GTF_DESTINATION AS DESTINATION_ACCOUNT',
            'CSC_RDN_DESC_TRANSFER AS DESC',
            'CSC_RDN_AMOUNT AS AMOUNT',
            'CSC_RDN_SUSPECT_PROCESS AS SUSPECT_PROCESS',
            'CSC_RDN_SUSPECT_PROCESS_VALUE AS SUSPECT_PROCESS_VALUE',
            'CSC_RDN_SUSPECT_UNPROCESS AS SUSPECT_UNPROCESS',
            'CSC_RDN_SUSPECT_UNPROCESS_VALUE AS SUSPECT_UNPROCESS_VALUE',
            'CSC_RDN_CORRECTION_PROCESS AS CORRECTION_PROCESS',
            'CSC_RDN_CORRECTION_PROCESS_VALUE AS CORRECTION_PROCESS_VALUE',
            'CSC_RDN_CORRECTION_UNPROCESS AS CORRECTION_UNPROCESS',
            'CSC_RDN_CORRECTION_UNPROCESS_VALUE AS CORRECTION_UNPROCESS_VALUE',
            'CSC_RDN_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
            'CSC_RDN_REAL_TRANSFER AS REAL_TRANSFER',
            'CSC_RDN_DIFF_TRANSFER AS DIFFERENT_TRANSFER',
            'CSC_RDN_TYPE AS TYPE',
            'CSC_RDN_USER_PROCESS AS USER_PROCESS',
            'CSC_RDN_STATUS AS STATUS',
        )
        ->summary()
        ->where('GB.CSC_GB_ID', $group)
        ->settledDt($date)
        ->where(
            function ($query) use ($type, $status) {
                // Filter Type
                if (null != $type) :
                    $query->type($type);
                endif;

                // Filter Status
                if (null != $status) :
                    $query->status($status);
                endif;
            }
        )
        ->groupBy('CSC_RDN_ID')
        ->paginate($items);

        // Hitung Jumlah Data
        $countData = count($data);

        // Null == false | !Null == data
        return (null != $countData) ? $data : false;
    }

    // Get Data List Recon By Id
    public function reconDanaListBiId($id, $searchBy = 'id')
    {
        if ('id' == $searchBy) : // Logic Get Data Search By Recon Dana ID
            $data = ReconDana::select(
                'CSC_RDN_ID AS ID',
                'CSC_RDN_BILLER AS BILLER_ID',
                'BILLER.CSC_BILLER_NAME AS BILLER',
                'CSC_RDN_GROUP_TRANSFER AS GROUP_TARNSFER_ID',
                'GTF.CSC_GTF_NAME AS GROUP_TRANSFER',
                'CSC_RDN_START_DT AS START',
                'CSC_RDN_END_DT AS END',
                'CSC_RDN_SETTLED_DT AS DATE_TRANSFER',
                'GTF.CSC_GTF_SOURCE AS SOURCE_ACCOUNT',
                'GTF.CSC_GTF_DESTINATION AS DESTINATION_ACCOUNT',
                'CSC_RDN_DESC_TRANSFER AS DESC',
                'CSC_RDN_AMOUNT AS AMOUNT',
                'CSC_RDN_SUSPECT_PROCESS AS SUSPECT_PROCESS',
                'CSC_RDN_SUSPECT_PROCESS_VALUE AS SUSPECT_PROCESS_VALUE',
                'CSC_RDN_SUSPECT_UNPROCESS AS SUSPECT_UNPROCESS',
                'CSC_RDN_SUSPECT_UNPROCESS_VALUE AS SUSPECT_UNPROCESS_VALUE',
                'CSC_RDN_CORRECTION_PROCESS AS CORRECTION_PROCESS',
                'CSC_RDN_CORRECTION_PROCESS_VALUE AS CORRECTION_PROCESS_VALUE',
                'CSC_RDN_CORRECTION_UNPROCESS AS CORRECTION_UNPROCESS',
                'CSC_RDN_CORRECTION_UNPROCESS_VALUE AS CORRECTION_UNPROCESS_VALUE',
                'CSC_RDN_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
                'CSC_RDN_REAL_TRANSFER AS REAL_TRANSFER',
                'CSC_RDN_DIFF_TRANSFER AS DIFFERENT_TRANSFER',
                'CSC_RDN_TYPE AS TYPE',
                'CSC_RDN_USER_PROCESS AS USER_PROCESS',
                'CSC_RDN_STATUS AS STATUS',
            )
            ->summary()
            ->id($id)
            ->first();

            // Null == false | !Null == data
            $response = (null != $data) ? $data : false;

        elseif ('gtf' == $searchBy) : // Logic Get Data Search By Group transfer
            $data = ReconDana::select(
                'CSC_RDN_ID AS ID',
                'CSC_RDN_BILLER AS BILLER_ID',
                'BILLER.CSC_BILLER_NAME AS BILLER',
                'CSC_RDN_GROUP_TRANSFER AS GROUP_TARNSFER_ID',
                'GTF.CSC_GTF_NAME AS GROUP_TRANSFER',
                'CSC_RDN_START_DT AS START',
                'CSC_RDN_END_DT AS END',
                'CSC_RDN_SETTLED_DT AS DATE_TRANSFER',
                'GTF.CSC_GTF_SOURCE AS SOURCE_ACCOUNT',
                'GTF.CSC_GTF_DESTINATION AS DESTINATION_ACCOUNT',
                'CSC_RDN_DESC_TRANSFER AS DESC',
                'CSC_RDN_AMOUNT AS AMOUNT',
                'CSC_RDN_SUSPECT_PROCESS AS SUSPECT_PROCESS',
                'CSC_RDN_SUSPECT_PROCESS_VALUE AS SUSPECT_PROCESS_VALUE',
                'CSC_RDN_SUSPECT_UNPROCESS AS SUSPECT_UNPROCESS',
                'CSC_RDN_SUSPECT_UNPROCESS_VALUE AS SUSPECT_UNPROCESS_VALUE',
                'CSC_RDN_CORRECTION_PROCESS AS CORRECTION_PROCESS',
                'CSC_RDN_CORRECTION_PROCESS_VALUE AS CORRECTION_PROCESS_VALUE',
                'CSC_RDN_CORRECTION_UNPROCESS AS CORRECTION_UNPROCESS',
                'CSC_RDN_CORRECTION_UNPROCESS_VALUE AS CORRECTION_UNPROCESS_VALUE',
                'CSC_RDN_AMOUNT_TRANSFER AS AMOUNT_TRANSFER',
                'CSC_RDN_REAL_TRANSFER AS REAL_TRANSFER',
                'CSC_RDN_DIFF_TRANSFER AS DIFFERENT_TRANSFER',
                'CSC_RDN_TYPE AS TYPE',
                'CSC_RDN_USER_PROCESS AS USER_PROCESS',
                'CSC_RDN_STATUS AS STATUS',
            )
            ->summary()
            ->groupTransfer($id)
            ->get();
        endif;

        // Hitung Jumlah Data

        // Null == false | !Null == data
        return (null != $data) ? $data : false;
    }

    // Get Data List Correction
    public function reconDanaCorrection($id, $items)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'B.CSC_BILLER_NAME AS BILLER',
            'GTF.CSC_GTF_NAME AS GROUP_TRANSFER',
            'CORR.CSC_CORR_DATE_TRANSFER AS DATE_TRANSFER',
            'CORR.CSC_CORR_CORRECTION_VALUE AS VALUE',
            'CORR.CSC_CORR_CORRECTION AS AMOUNT',
            'CORR.CSC_CORR_DESC AS DESC',
        )
        ->join(
            'CSCCORE_BILLER AS B',
            'CSC_RDN_BILLER',
            '=',
            'B.CSC_BILLER_ID'
        )
        ->whereNull('B.CSC_BILLER_DELETED_DT')
        ->join(
            'CSCCORE_CORRECTION AS CORR',
            'CSC_RDN_ID',
            '=',
            'CORR.CSC_CORR_RECON_DANA_ID'
        )
        ->whereNull('CORR.CSC_CORR_DELETED_DT')
        ->join(
            'CSCCORE_GROUP_TRANSFER_FUNDS AS GTF',
            'CORR.CSC_CORR_GROUP_TRANSFER',
            '=',
            'GTF.CSC_GTF_ID'
        )
        ->whereNull('GTF.CSC_GTF_DELETED_DT')
        ->id($id)
        ->paginate($items);

        // Hitung Data
        $countData = count($data);

        // Null = false | !Null = data
        return (null == $countData) ? false : $data;
    }

    // Get Data List Suspect
    public function reconDanaSuspect($id, $items)
    {
        // Logic Get Data
        $data = ReconDana::select(
            'TRX.CSM_TC_SUBID AS CUSTOMER_ID',
            'TRX.CSM_TC_SUBID AS CUSTOMER_NAME',
            'TRX.CSM_TC_CID AS CID',
            'TRX.CSM_TC_TRX_DT AS TRX_DATE',
            'TRX.CSM_TC_PROCESS_DT AS PROCESS_DATE',
            'TRX.CSM_TC_NBILL AS NBILL',
            'TRX.CSM_TC_NMONTH AS NMONTH',
            'TRX.CSM_TC_FEE AS FEE',
            'TRX.CSM_TC_FEE_ADMIN AS FEE_ADMIN',
            'TRX.CSM_TC_FEE_ADMIN_AMOUNT AS FEE_ADMIN_AMOUNT',
            DB::raw('TRX.CSM_TC_FEE + TRX.CSM_TC_FEE_ADMIN AS TOTAL'),
            'TRX.CSM_TC_FEE_BILLER AS FEE_BILLER',
            'TRX.CSM_TC_FEE_BILLER_AMOUNT AS FEE_BILLER_AMOUNT',
            'TRX.CSM_TC_FEE_VSI AS FEE_VSI',
            'TRX.CSM_TC_FEE_VSI_AMOUNT AS FEE_VSI_AMOUNT',
            'TRX.CSM_TC_BILLER_AMOUNT AS BILLER_AMOUNT',
            'FH.CSC_FH_FORMULA AS FORMULA_TRANSFER',
            'TRX.CSM_TC_CLAIM_VSI AS CLAIM_VSI',
            'TRX.CSM_TC_CLAIM_VSI_AMOUNT AS CLAIM_VSI_AMOUNT',
            'TRX.CSM_TC_CLAIM_PARTNER AS CLAIM_PARTNER',
            'TRX.CSM_TC_CLAIM_PARTNER_AMOUNT AS CLAIM_PARTNER_AMOUNT',
            //
            'TRX.CSM_TC_SW_REFNUM AS SW_REFNUM',
            'TRX.CSM_TC_STATUS_TRX AS STATUS_TRX',
            'TRX.CSM_TC_STATUS_FUNDS AS STATUS_FUNDS',
            // Customer Area
            'TD.CSC_TD_NAME AS PRODUCT',
            'TD.CSC_TD_TABLE AS TABLE',
            'TRX.CSM_TC_SUBID AS SUBID',
            'TD.CSC_TD_SUBID_COLUMN AS SUBID_COLUMN',
            // 'TRX.CSM_TC_SW_REFNUM AS SW_REFNUM',
            'TD.CSC_TD_SWITCH_REFNUM_COLUMN AS REFNUM_COLUMN'
        )
        ->join(
            'CSCCORE_TRX_CORRECTION AS TRX',
            'CSC_RDN_ID',
            'TRX.CSM_TC_RECON_DANA_ID'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TD',
            'TRX.CSM_TC_PRODUCT',
            '=',
            'TD.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_FORMULA_TRANSFER AS FH',
            'TRX.CSM_TC_FORMULA_TRANSFER',
            '=',
            'FH.CSC_FH_ID'
        )
        ->whereNull('TD.CSC_TD_DELETED_DT')
        ->id($id)
        ->paginate($items);

        // Hitung Data
        $count = count($data);

        // Null = false | !Null = data
        return (null != $count) ? $data : false;
    }

    // Check Status Suspect
    public function reconDanaCheckSuspect($danaId, $date, $type)
    {
        /***
         * Type = date  | spesifik mencari di tanggal tertentu
         * Type = range | mencari berdasarkan tanggal A sampai tanggal N
         */

        if ('range' == $type) :
            $data = TrxCorrection::select('CSM_TC_PRODUCT AS PRODUCT')
            ->nullReconData()
            ->reconDana($danaId)
            ->dateRange($date)
            ->first();
        elseif ('date' == $type) :
            $data = TrxCorrection::select('CSM_TC_PRODUCT AS PRODUCT')
            ->nullReconData()
            ->reconDana($danaId)
            ->date($date)
            ->first();
        endif;

        // Null == 1 | !Null == 0
        return (null == $data) ? 1 : 0;
    }

    // Check Status Correction
    public function reconDanaCheckCorrection($danaId, $date, $type)
    {
        /***
         * Type = date  | spesifik mencari di tanggal tertentu
         * Type = range | mencari berdasarkan tanggal A sampai tanggal N
         */

        if ('date' == $type) :
            $data = CoreCorrection::getData()
            ->reconDana($danaId)
            ->date($date)
            ->first('CSC_CORR_RECON_DANA_ID AS DANA_ID');
        elseif ('range' == $type) :
            $data = CoreCorrection::getData()
            ->reconDana($danaId)
            ->dateRange($date)
            ->first('CSC_CORR_RECON_DANA_ID AS DANA_ID');
        endif;

        // Null == 1 | !Null == 0
        return (null == $data) ? 1 : 0;
    }

    // Get By Id Suspect
    public function reconDanaByIdSuspect($id, $date, $product = null, $status = null)
    {
        // Inisialisasi Field Select Data
        $field = [
            'CSM_TC_PRODUCT AS PRODUCT',
            DB::raw('SUM(CSM_TC_NBILL) AS NBILL'),
            DB::raw('SUM(CSM_TC_NMONTH) AS NMONTH'),
            DB::raw('SUM(CSM_TC_FEE) AS FEE'),
            DB::raw('SUM(CSM_TC_FEE_ADMIN) AS FEE_ADMIN'),
            DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS TRX_FEE_ADMIN_AMOUNT'),
            DB::raw('SUM(CSM_TC_FEE + CSM_TC_FEE_ADMIN) AS TOTAL_FEE'),
            DB::raw('SUM(CSM_TC_FEE_BILLER) AS FEE_BILLER'),
            DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
            DB::raw('SUM(CSM_TC_FEE_VSI) AS FEE_VSI'),
            DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
            DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS BILLER_AMOUNT'),
            'FH.CSC_FH_FORMULA AS FORMULA_TRANSFER',
            DB::raw('SUM(CSM_TC_CLAIM_VSI)AS CLAIM_VSI'),
            DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT)AS CLAIM_VSI_AMOUNT'),
            DB::raw('SUM(CSM_TC_CLAIM_PARTNER)AS CLAIM_PARTNER'),
            DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT)AS CLAIM_PARTNER_AMOUNT'),
        ];

        /***
         * Status = null | Query Group By Status TRX
         * Status = 0 | Dilunaskan/Paid
         * Status = 1 | Dibatalkan/Canceled
         */

        if (isset($status) && isset($product)) :
            $data = TrxCorrection::select($field)
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FH',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FH.CSC_FH_ID'
            )
            ->reconDana($id)
            ->product($product)
            ->dateRange($date)
            ->status($status)
            ->groupBy('CSM_TC_PRODUCT')
            ->orderBy('CSM_TC_PRODUCT')
            ->get();
        elseif (null == $status && null == $product) :
            $data = TrxCorrection::select($field)
            ->join(
                'CSCCORE_FORMULA_TRANSFER AS FH',
                'CSM_TC_FORMULA_TRANSFER',
                '=',
                'FH.CSC_FH_ID'
            )
            ->reconDana($id)
            ->dateRange($date)
            ->groupBy('CSM_TC_PRODUCT')
            ->orderBy('CSM_TC_PRODUCT')
            ->get();
        endif;

        // Hitung Jumlah Data
        $count = count($data);

        // Null == false | !Null == $data
        return (null == $count) ? false : $data;
    }
}
