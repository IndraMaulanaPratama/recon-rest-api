<?php

namespace App\Traits;

use App\Models\TrxCorrection;
use Illuminate\Support\Facades\DB;

trait TrxCorrectionTraits
{
    use ResponseHandler;

    // Response Not Found
    public function trxNotFound()
    {
        return $this->responseNotFound('Data Trx Correction Not Found');
    }

    public function trxDanaIdSettledDt($id, $date)
    {
        $field = [
            'CSM_TC_PRODUCT AS PRODUCT',
            DB::raw('SUM(CSM_TC_NBILL) AS NBILL'),
            DB::raw('SUM(CSM_TC_NMONTH) AS NMONTH'),

            DB::raw('SUM(CSM_TC_FEE) AS RpTag'),
            DB::raw('SUM(CSM_TC_FEE_ADMIN) AS ADMIN'),
            DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS ADMIN_AMOUNT'),
            DB::raw('SUM(CSM_TC_FEE + CSM_TC_FEE_ADMIN) AS TOTAL'),

            DB::raw('SUM(CSM_TC_FEE_BILLER) AS BILLER'),
            DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS BILLER_AMOUNT'),

            DB::raw('SUM(CSM_TC_FEE_VSI) AS VSI'),
            DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS VSI_AMOUNT'),

            DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS BILLER_AMOUNT'),

            'FH.CSC_FH_FORMULA AS FORMULA_TRANSFER',

            DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CLAIM_VSI'),
            DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),

            DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CLAIM_PARTNER'),
            DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),

            'CSM_TC_STATUS_TRX AS STATUS'
        ];

        // Logic Get Data
        $data = TrxCorrection::reconDana($id)->dateRange($date)
        ->joinFormulaTransfer()
        ->groupBy('CSM_TC_PRODUCT')
        ->groupBy('CSM_TC_STATUS_TRX')
        ->get($field);

        // Null == false || !Null == data
        $count = count($data);
        return (null == $count) ? false : $data;
    }
}
