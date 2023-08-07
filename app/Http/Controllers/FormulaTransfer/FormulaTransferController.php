<?php

namespace App\Http\Controllers\FormulaTransfer;

use App\Http\Controllers\Controller;
use App\Models\CoreFormulaTransfer;
use App\Traits\FormulaTransferTraits;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormulaTransferController extends Controller
{
    use ResponseHandler;
    use FormulaTransferTraits;

    public function simpleField()
    {
        return [
                'CSC_FH_ID AS ID',
                'CSC_FH_FORMULA AS FORMULA',
            ];
    }

    public function detailField()
    {
        return [
                'CSC_FH_ID AS ID',
                'CSC_FH_FORMULA AS FORMULA',
                'CSC_FH_STATUS AS STATUS',
            ];
    }

    public function getList(Request $request, $config)
    {
        // Logic Simple Config
        if ('simple' == $config) :
            // Logic Get Data
            $data = CoreFormulaTransfer::getData()
            ->get($this->simpleField());

            // Response Sukses
            if (null != count($data)) :
                return $this->generalConfigResponse(
                    200,
                    'Get List Formula Transfer Success',
                    $config,
                    $data
                );
            endif;

            // Response Not Found
            if (null == count($data)) :
                return $this->generalResponse(
                    404,
                    'Data Formula Transfer Not Found'
                );
            endif;

            // Response Failed
            if (!$data) :
                return $this->generalResponse(
                    500,
                    'Get List Formula Transfer Failed'
                );
            endif;
        endif;

        // Logic Detail Config
        if ('detail' == $config) :
            // Inisialisasi Variable
            $items = (null == $request->items) ? 10 : $request->items;

            // Logic Get Data
            $data = CoreFormulaTransfer::getData()->paginate(
                $items,
                $this->detailField()
            );

            // Response Sukses
            if (null != count($data)) :
                return $this->generalConfigResponse(
                    200,
                    'Get List Formula Transfer Success',
                    $config,
                    $data
                );
            endif;

            // Response Not Found
            if (null == count($data)) :
                return $this->generalResponse(
                    404,
                    'Data Formula Transfer Not Found'
                );
            endif;

            // Response Failed
            if (!$data) :
                return $this->generalResponse(
                    500,
                    'Get List Formula Transfer Failed'
                );
            endif;
        endif;

        // Logic Undefined Config
        if ('simple' != $config && 'detail' != $config) :
            return $this->generalResponse(
                404,
                'Data Formula Transfer Not Found'
            );
        endif;
    }

    public function getData(Request $request)
    {
        // Vlidasi Data Mandatori
        try {
            $request->validate(
                [
                    'id' => ['required', 'numeric', 'digits_between:1,11'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $id = $request->id;

        // Logic Get Data Formula Transfer
        $data = CoreFormulaTransfer::getData()
        ->searchData($id)
        ->first($this->simpleField());

        // Response Success
        if (null != $data) :
            return $this->generalDataResponse(
                200,
                'Get Data Formula Transfer Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == $data) {
            return $this->generalResponse(
                404,
                'Data Formula Transfer Not Found'
            );
        }

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                200,
                'Get Data Formula Transfer Failed'
            );
        endif;
    }

    public function addData(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'formula' => ['required', 'string', 'max:255'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $formula = $request->formula;
        $status = 0;

        // Logic Add Data
        $data = CoreFormulaTransfer::create(
            [
                'CSC_FH_FORMULA' => $formula,
                'CSC_FH_STATUS' => $status,
            ]
        );

        // Response Sukses
        if ($data) :
            return $this->generalResponse(
                200,
                'Insert Data Formula Transfer Success'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Insert Data Formula Transfer Failed'
            );
        endif;
    }

    public function updateData(Request $request, $id)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'formula' => ['required', 'string', 'max:255'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->generalDataResponse(
                400,
                'Invalid Data Validation',
                $th->validator->errors()
            );
        }

        // Inisialisasi Variable
        $formula = $request->formula;

        // Cek Data Formula
        $data = CoreFormulaTransfer::searchData($id)->first();
        if (null == $data) :
            return $this->generalResponse(
                404,
                'Data Formula Transfer Not Found'
            );
        endif;

        // Logic Update Data
        $data->CSC_FH_FORMULA = $formula;
        $data->save();

        // Response Sukses
        if ($data) :
            return $this->generalResponse(
                200,
                'Update Data Formula Transfer Success'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Update Data Formula Transfer Failed'
            );
        endif;
    }

    public function deleteData($id)
    {
        // Cek Data
        $data = CoreFormulaTransfer::searchData($id)->first();
        if (null == $data) :
            return $this->generalResponse(
                404,
                'Data Formula Transfer Not Found'
            );
        endif;

        // Logic Delete Data
        $data->CSC_FH_STATUS = 1;
        $data->save();

        // Response Sukses
        if ($data) :
            return $this->generalResponse(
                200,
                'Delete Data Formula Transfer Success'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Delete Data Formula Transfer Failed'
            );
        endif;
    }

    public function destroyData($id)
    {
        // Cek Data
        $data = CoreFormulaTransfer::where('CSC_FH_ID', $id)->first();
        if (null == $data) :
            return $this->generalResponse(
                404,
                'Data Formula Transfer Not Found'
            );
        endif;

        // Logic Delete Data
        $data->delete();

        // Response Sukses
        if ($data) :
            return $this->generalResponse(
                200,
                'Delete Data Formula Transfer Success'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Delete Data Formula Transfer Failed'
            );
        endif;
    }

    public function filterData(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'id' => ['numeric', 'digits_between:1,11'],
                    'formula' => ['string', 'max:255'],
                    'items' => ['numeric', 'max:100'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Data
        $data = CoreFormulaTransfer::where(function ($query) use ($request) {
            // Filter by id
            if (null != $request->id) :
                $query->searchData($request->id);
            endif;

            // Filter by formula
            if (null != $request->formula) :
                $query->searchFormula($request->formula);
            endif;

            $query->where('CSC_FH_STATUS', 0);
        })
        ->paginate(
            $items,
            $this->detailField()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Filter Data Formula Transfer Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Filter Data Formula Transfer Not Found'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Filter Data Formula Transfer Failed'
            );
        endif;
    }

    public function trash(Request $request)
    {
        // Validasi Data Mandatori
        try {
            $request->validate(
                [
                    'id' => ['numeric', 'digits_between:1,11'],
                    'formula' => ['string', 'max:255'],
                    'items' => ['numeric', 'max:100'],
                ]
            );
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic Get Data
        $data = CoreFormulaTransfer::where(function ($query) use ($request) {
            // Filter by id
            if (null != $request->id) :
                $query->searchData($request->id);
            endif;

            // Filter by formula
            if (null != $request->formula) :
                $query->searchFormula($request->formula);
            endif;

            $query->where('CSC_FH_STATUS', 1);
        })
        ->paginate(
            $items,
            $this->detailField()
        );

        // Add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        if (null != count($data)) :
            return $this->generalDataResponse(
                200,
                'Get Data Trash Formula Transfer Success',
                $data
            );
        endif;

        // Response Not Found
        if (null == count($data)) :
            return $this->generalResponse(
                404,
                'Data Trash Formula Transfer Not Found'
            );
        endif;

        // Response Failed
        if (!$data) :
            return $this->generalResponse(
                500,
                'Get Data Trash Formula Transfer Failed'
            );
        endif;
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'id' => ['required', 'array'],
                'id.*' => ['numeric', 'digits_between:1,11'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $id = $request->id;
        $count = count($id);
        $notFound = [];

        // Check Data
        for ($i=0; $i < $count; $i++) {
            $checkFormula = $this->formulaSearchDeletedData($id[$i]);

            // Validasi Data
            if (false == $checkFormula) :
                $notFound = $id[$i];
                unset($id[$i]);
            endif;
        }

        // Recounting dan Reordering Request Data
        $id = array_values($id);
        $count = count($id);

        // Response  Not Found
        if (null == $count) :
            return $this->formulaNotFound();
        endif;

        // Logic Restore Data
        try {
            for ($n=0; $n < $count; $n++) :
                $data = $this->formulaSearchDeletedData($id[$n]);
                $data->CSC_FH_STATUS = 0;
                $data->save();
            endfor;
        } catch (\Throwable $th) {
            return $this->responseDataFailed('Restore Data Formula Transfer Failed', $th->getMessage());
        }

        // Response Success With Warning
        if (null != $notFound) :
            $response['id'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Formula Transfer Success But Some Data Not Found',
                $response
            );
        endif;

        // Response Success
        return $this->generalResponse(200, 'Restore Data Formula Transfer Success');
    }
}
