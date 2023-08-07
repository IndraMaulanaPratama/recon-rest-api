<?php

namespace App\Traits;

use App\Models\CoreDownCentral;
use App\Traits\ResponseHandler;

trait CidTraits
{
    use ResponseHandler;

    // Response Not Found
    public function cidNotFound()
    {
        return $this->responseNotFound('Data CID Not Found');
    }

    // Check Data Cid
    public function cidCheckData($id)
    {
        $data = CoreDownCentral::searchData($id)->first('CSC_DC_NAME AS NAME');

        // null = false | !null = $data
        return (false != $data) ? $data : false;
    }
}
