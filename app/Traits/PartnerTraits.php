<?php

namespace App\Traits;

use App\Models\CorePartner;

trait PartnerTraits
{
    use ResponseHandler;

    // Response Partner Not Found
    public function partnerNotFound()
    {
        return $this->responseNotFound('Data Partner Not Found');
    }

    // Search Deleted Data
    public function partnerGetDeletedData($id)
    {
        // Logic Get Data
        $data = CorePartner::searchTrashData($id)->first();

        // Null == false | !Null = data
        return (null == $data) ? false : $data;
    }
}
