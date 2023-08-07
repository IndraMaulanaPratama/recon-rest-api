<?php

namespace App\Traits;

use App\Models\CoreProfileFee;

trait ProfileTraits
{
    use ResponseHandler;

    public function profileNotFound()
    {
        return $this->responseNotFound('Data Profile Fee Not Found');
    }

    public function profileById($id)
    {
        // Logic Get Data
        $data = CoreProfileFee::searchData($id)->first('CSC_PROFILE_NAME AS NAME');

        // Null == False | !Null == $data
        return (null == $data) ? false : $data;
    }

    // Search Deleted Data
    public function profileSearchDeletedData($id)
    {
        // Logic Get Data
        $data = CoreProfileFee::searchTrashData($id)->first();

        // Null == false | !Null = data
        return (null == $data) ? false : $data;
    }
}
