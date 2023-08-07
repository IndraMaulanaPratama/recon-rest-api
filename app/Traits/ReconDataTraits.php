<?php

namespace App\Traits;

use App\Models\CoreReconData;
use App\Models\TrxCorrection;
use App\Traits\ResponseHandler;

trait ReconDataTraits
{
    use ResponseHandler;

    // Response Not Found
    public function reconDataNotFound()
    {
        return $this->responseNotFound('List Recon Data Not Found');
    }

    // Search Data By Product
    public function reconDataProduct($product)
    {
        // Logic Get Data
        $data = CoreReconData::product($product)->first('CSC_RDT_PRODUCT AS PRODUCT');

        // Null = false | !Null = data
        return (null != $data) ? $data : false;
    }

    // Search Data By Product and Date Range
    public function reconDataProductDateRange($product, $date)
    {
        // Logic Get Data
        $data = CoreReconData::product($product)
        ->dateRange($date)
        ->first('CSC_RDT_PRODUCT AS PRODUCT');

        // Null = false | !Null = data
        return (null != $data) ? $data : false;
    }

    // Get Data History
    public function reconDataGetHistory($product, $cid, $date, $items)
    {
        $data = CoreReconData::getHistory($product, $cid, $date)->paginate($items);
        $countData = count($data);

        // null = false | !null = $data
        return (null != $countData) ? $data : false;
    }

    // Check Product Dan Status
    public function reconDataSearchProductStatus($product, $status)
    {
        // Logic Get Data
        $data = CoreReconData::product($product)->status($status)->first('CSC_RDT_ID');

        // Null == false | !Null == $data
        return (null != $data) ? $data : false;
    }

    // Get Field Status Suspect
    public function reconDataCheckSuspect($product, $date)
    {
        // Logic Get Data
        $data = TrxCorrection::select('CSM_TC_PRODUCT AS PRODUCT')
        ->nullReconData()
        ->product($product)
        ->dateRange($date)
        ->first();

        // Null == 1 | !Null == 0
        return (null == $data) ? 1 : 0;
    }
}
