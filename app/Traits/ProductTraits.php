<?php

namespace App\Traits;

use App\Models\TransactionDefinitionV2;
use App\Traits\ResponseHandler;

trait ProductTraits
{
    use ResponseHandler;

    // Response Not Found
    public function productNotFound()
    {
        return $this->responseNotFound('Data Product/Area Not Found');
    }

    // Cek Data Product
    public function productCheckData($name)
    {
        $data = TransactionDefinitionV2::searchData($name)->first('CSC_TD_NAME AS NAME');

        // null = false | !null = $data
        return (null != $data) ? $data : false;
    }
}
