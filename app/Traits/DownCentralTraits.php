<?php

namespace App\Traits;

use App\Models\CoreDownCentral;

trait DownCentralTraits
{
    use ResponseHandler;

    // Response Data Down Central Not Found
    public function cidNotFound()
    {
        return $this->responseNotFound('Data CID Not Found');
    }

    // Search Data Down Central By Id
    public function cidById($id)
    {
        // Logic Get Data
        $data = CoreDownCentral::searchData($id)->first();

        // Null == false | !Null == $data
        return (null == $data) ? false : $data;
    }

    // Search trash Data Down Central
    public function cidTrashData($id)
    {
        // Logic Get Data
        $data = CoreDownCentral::searchTrashData($id)->first();

        // Null == false | !Null == $data
        return (null == $data) ? false : $data;
    }
}
