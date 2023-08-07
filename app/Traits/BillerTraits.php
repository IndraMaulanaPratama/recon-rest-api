<?php

namespace App\Traits;

use App\Models\CoreBiller;
use App\Traits\ResponseHandler;

trait BillerTraits
{
    use ResponseHandler;

    // Response Biller Not Found
    public function billerNotFound()
    {
        return $this->responseNotFound('Data Biller Not Found');
    }

    // Config Simple Field
    protected function billerSimpleField()
    {
        return [
            'CSC_BILLER_ID AS ID',
            'CSC_BILLER_GROUP_PRODUCT AS GROUP_OF_PRODUCT',
            'CSC_BILLER_NAME AS NAME',
            'CSC_BILLER_PROFILE AS PROFILE',
            'PF.CSC_PROFILE_NAME AS PROFILE_NAME',
        ];
    }

    // Get Data Biller
    public function billerGetData($id)
    {
        // Get Data
        $data = CoreBiller::joinProfileFee()
        ->searchData($id)->first($this->billerSimpleField());

        // Null == False | !Null == True
        return (null != $data) ? $data : false;
    }

    // Search By Id Biller
    public function billerById($id)
    {
        // Get Data
        $data = CoreBiller::searchData($id)->first();

        // Null == False | !Null == True
        return (null != $data) ? $data : false;
    }

    // Search Trash Data
    public function billerGetTrashData($id)
    {
        // Logic get data
        $data = CoreBiller::searchTrashData($id)->first();

        // Null == False || !Null == $data
        return (null == $data) ? false : $data;
    }
}
