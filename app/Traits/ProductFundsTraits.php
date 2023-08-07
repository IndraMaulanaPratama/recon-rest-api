<?php

namespace App\Traits;

use App\Models\CoreProductFunds;

trait ProductFundsTraits
{
    use ResponseHandler;

    // Check Data Exists
    public function productFundsCheckExists($product, $grouop)
    {
        // Logic Get Data
        $data = CoreProductFunds::checkExists($product, $grouop)->first();

        // Null == true | !Null == false
        return (null == $data) ? true : false;
    }
}
