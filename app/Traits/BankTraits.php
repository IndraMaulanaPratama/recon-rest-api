<?php

namespace App\Traits;

use App\Models\CoreBank;

trait BankTraits
{
    use ResponseHandler;

    // Response Bank Not Found
    public function bankNotFound()
    {
        return $this->responseNotFound('Data Bank Not Found');
    }

    // Search Deleted Data Bank
    public function bankSearchDeletedData($id)
    {
        // Logic Get Deleted Data
        $data = CoreBank::searchTrashData($id)->first();

        // Null == false | !Null = data
        return (null == $data) ? false : $data;
    }
}
