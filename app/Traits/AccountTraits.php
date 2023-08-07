<?php

namespace App\Traits;

use App\Models\CoreAccount;

trait AccountTraits
{
    use ResponseHandler;

    // Response Account Not Found
    public function accountNotFound()
    {
        return $this->responseNotFound('Data Account Not Found');
    }

    // Get Deleted Data Account
    public function accountSearchDeletedData($id)
    {
        // Logic Get Data
        $data = CoreAccount::searchTrashData($id)->first();

        // Null = False | !Null = data
        return (false == $data) ? false : $data;
    }
}
