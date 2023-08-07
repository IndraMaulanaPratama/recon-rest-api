<?php

namespace App\Traits;

use App\Models\CoreFormulaTransfer;

trait FormulaTransferTraits
{
    use ResponseHandler;

    // Response Not Found
    public function formulaNotFound()
    {
        return  $this->responseNotFound('Data Formula Transfer Not Found');
    }

    // Search Deleted Data
    public function formulaSearchDeletedData($id)
    {
        // Logic Get data
        $data = CoreFormulaTransfer::searchTrashData($id)->first();

        // Null == false | !Null = data
        return (null == $data) ? false : $data;
    }
}
