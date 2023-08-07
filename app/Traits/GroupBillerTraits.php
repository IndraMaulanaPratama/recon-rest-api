<?php

namespace App\Traits;

use App\Models\CoreBillerCollection;
use App\Models\CoreGroupBiller;
use App\Traits\ResponseHandler;

trait GroupBillerTraits
{
    use ResponseHandler;

    // Response Group Biller Not Found
    public function groupBillerNotFound()
    {
        return $this->responseNotFound('Data Group Biller Not Found');
    }

    // Response Grouop Biller Exists
    public function groupBillerCollectionExists()
    {
        return $this->generalResponse(409, 'Insert Data Group Biller-Biller Exists');
    }

    // Config Simple Field
    protected function groupBillerSimpleField()
    {
        return [
            'CSC_GB_ID AS ID',
            'CSC_GB_NAME AS NAME',
        ];
    }

    // Get Data Group Biller
    public function groupBillerGetData($id)
    {
        // Logic Get Data
        $data = CoreGroupBiller::getData()
        ->searchData($id)
        ->first($this->groupBillerSimpleField());

        // Null == False | !Null == $data
        return (null != $data) ? $data : false;
    }

    // Search By Id Group Biller
    public function groupBillerById($id)
    {
        // Logic Get Data
        $data = CoreGroupBiller::getData()
        ->searchData($id)
        ->first();

        // Null == False | !Null == $data
        return (null != $data) ? $data : false;
    }

    // Check Data Exists
    public function groupBillerCheckExists($biller, $groupBiller)
    {
        // Logic Get Data
        $data = CoreBillerCollection::searchByGroupAndBiller($groupBiller, $biller)->first();

        // empty($biller) & empty($groupBiller) = true | false;
        return (null == $data) ? true : false;
    }

    // Search Deleted Data
    public function groupBillerSearchDeletedData($id)
    {
        // Logic Get Data
        $data = CoreGroupBiller::searchTrashdata($id)->first();

        // Null == False | !Null == data
        return (false == $data) ? false : $data;
    }
}
