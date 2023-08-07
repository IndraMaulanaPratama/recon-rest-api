<?php

namespace App\Traits;

use App\Models\CoreGroupTransferFunds;

trait GroupTransferTraits
{
    use ResponseHandler;

    // Response Group transfer Not Found
    public function groupTransferNotFound()
    {
        return $this->responseNotFound('Data Group Transfer Funds Not Found');
    }

    // Check Data Group Transfer
    public function groupTransferById($id)
    {
        // Logic get Data
        $data = CoreGroupTransferFunds::searchData($id)->first();

        // Null == false | !Null == True
        return (null != $data) ? $data : false;
    }

    // Check Data Group Transfer
    public function groupTransferSearchDeletedData($id)
    {
        // Logic get Data
        $data = CoreGroupTransferFunds::searchTrashData($id)->first();

        // Null == false | !Null == True
        return (null != $data) ? $data : false;
    }
}
