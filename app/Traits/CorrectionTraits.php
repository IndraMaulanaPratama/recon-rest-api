<?php

namespace App\Traits;

use App\Models\CoreCorrection;

trait CorrectionTraits
{
    use ResponseHandler;

    // Response Not Found
    public function correctionNotFound()
    {
        return $this->responseNotFound('Data Correction Not Found');
    }

    // Search Data By Id
    public function correctionById($id)
    {
        // Logic get Data
        $data = CoreCorrection::select(
            'CSC_CORR_ID',
            'CSC_CORR_CORRECTION',
            'CSC_CORR_CORRECTION_VALUE',
            'CSC_CORR_STATUS',
            'CSC_CORR_RECON_DANA_ID',
            'CSC_CORR_DATE_PINBUK',
        )
        ->searchData($id)
        ->whereNull('CSC_CORR_RECON_DANA_ID')
        ->where('CSC_CORR_STATUS', 1)
        ->first();

        // null == false | !null == data
        return (null != $data) ? $data : false;
    }

    // Search Deleted Data
    public function correctionSearchDeletedData($id)
    {
        // Logic Get Data
        $data = CoreCorrection::searchTrashData($id)->first();

        // Null == False | !Null == data
        return (null == $data) ? false : $data;
    }
}
