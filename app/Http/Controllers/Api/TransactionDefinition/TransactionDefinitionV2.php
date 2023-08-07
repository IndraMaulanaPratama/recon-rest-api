<?php

namespace App\Http\Controllers\Api\TransactionDefinition;

use App\Http\Controllers\Controller;
use App\Models\TransactionDefinitionV2 as ModelsTransactionDefinitionV2;
use Illuminate\Http\Request;
use App\Traits\ProductV2Traits;
use App\Traits\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TransactionDefinitionV2 extends Controller
{
    use ProductV2Traits;
    use ResponseHandler;

    public function index(Request $request, $config)
    {
        $count = null;

        if ('simple' === $config) :
            // Logic Get Data
            try {
                $data = ModelsTransactionDefinitionV2::getData()->get($this->productSimpleField());
            } catch (\Throwable $th) {
                return $this->generalDataResponse(500, 'Get List Product/Area Failed', $th->getMessage());
            }

            // Hitung Jumlah Data
            $count = count($data);
        elseif ('detail' === $config) :
            // Validasi Data Mandatory
            try {
                $request->validate([
                    'items' => ['numeric', 'digits_between:1,8'],
                ]);
            } catch (ValidationException $th) {
                return $this->invalidValidation($th->validator->errors());
            }

            // Inisialisasi Variable
            $items = (null == $request->items) ? 10 : $request->items;

            // Logic get Data
            try {
                $data = ModelsTransactionDefinitionV2::getData()->paginate($items, $this->productDetailField());
            } catch (\Throwable $th) {
                return $this->generalDataResponse(500, 'Get List Product/Area Failed', $th->getMessage());
            }

            // Hitung Jumlah Data
            $count = count($data);

            // Add Index Number
            if (null != $count) :
                $data = $this->addIndexNumber($data);
            endif;
        endif;

        // Response Not Found
        if (null == $count) :
            return $this->responseNotFound('Data Product/Area Not Found');
        endif;

        // Response Success
        return $this->generalConfigResponse(200, 'Get List Product/Area Success', $config, $data);
    }

    public function store(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'biller_id' => ['string', 'max:255'],
                'group_name' => ['required', 'string', 'max:100'],
                'alias_name' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:100'],
                'find_criteria' => ['required', 'string'],
                'pan' => ['string', 'max:5'],
                'created_by' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;
        $biller = $request->biller_id;
        $groupName = $request->group_name;
        $alias = $request->alias_name;
        $description = $request->description;
        $findCriteria = $request->find_criteria;
        $pan = $request->pan;
        $created_by = $request->created_by;

        // Check Product Exists
        $exists = $this->productCheckData($name);

        // Response Data Exists
        if (false != $exists) :
            return $this->generalResponse(409, 'Data Product/Area Exists');
        endif;

        // Check Status Deleted
        $deleted = $this->productCheckDeletedData($name);

        // Response Status Data Deleted
        if (true == $deleted) :
            return $this->generalResponse(422, 'Unprocessable Entity');
        endif;

        // Logic Insert data
        try {
            $field = [
                'CSC_TD_NAME' => $name,
                'CSC_TD_BILLER_ID' => $biller,
                'CSC_TD_GROUPNAME' => $groupName,
                'CSC_TD_ALIASNAME' => $alias,
                'CSC_TD_DESC' => $description,
                'CSC_TD_FINDCRITERIA' => $findCriteria,
                'CSC_TD_PAN' => $pan,
                'CSC_TD_CREATED_BY' => $created_by,
                'CSC_TD_CREATED_DT' => Carbon::now('Asia/Jakarta'),
            ];

            $data = ModelsTransactionDefinitionV2::create($field);
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Insert Data Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Insert Data Product/Area Success');
    }

    public function show(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:100'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;

        // Logic Get Data
        try {
            $data = $this->productGetData($name, 'data');
        } catch (\Throwable $th) {
            return $this->failedResponse('Get Data Product/Area Failed');
        }

        // Response Not Found
        if (false == $data) :
            return $this->productNotFound();
        endif;

        // Response Success
        return $this->generalDataResponse(200, 'Get Data Product/Area Success', $data);
    }

    public function update(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'biller_id' => ['string', 'max:255'],
                'group_name' => ['required', 'string', 'max:100'],
                'alias_name' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:100'],
                'find_criteria' => ['required', 'string'],
                'pan' => ['string', 'max:5'],
                'modified_by' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;
        $biller = $request->biller_id;
        $groupName = $request->group_name;
        $alias = $request->alias_name;
        $description = $request->description;
        $findCriteria = $request->find_criteria;
        $pan = $request->pan;
        $modifiedBy = $request->modified_by;

        // Logic Get Data
        $checkData = $this->productCheckData($name);

        // Response Not Found
        if (false == $checkData) :
            return $this->productNotFound();
        endif;

        // Logic Update Data
        try {
            $data = ModelsTransactionDefinitionV2::searchData($name)->first();
            $data->CSC_TD_BILLER_ID = $biller;
            $data->CSC_TD_GROUPNAME = $groupName;
            $data->CSC_TD_ALIASNAME = $alias;
            $data->CSC_TD_DESC = $description;
            $data->CSC_TD_FINDCRITERIA = $findCriteria;
            $data->CSC_TD_PAN = $pan;
            $data->CSC_TD_CREATED_BY = $modifiedBy;
            $data->CSC_TD_CREATED_DT = Carbon::now('Asia/Jakarta');
            $data->save();
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Update Data Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Update Data Product/Area Success');
    }

    public function destroy(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'deleted_by' => ['required', 'string', 'max:50'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;
        $deletedBy = $request->deleted_by;
        $deletedDt = Carbon::now('Asia/Jakarta');

        // Logic Get Data Product
        $data = $this->productGetData($name, 'data');

        // Response Not Found
        if (false == $data) :
            return $this->productNotFound();
        endif;

        // Logic Delete Data
        try {
            $data = ModelsTransactionDefinitionV2::searchData($name)->first();
            $data->CSC_TD_DELETED_BY = $deletedBy;
            $data->CSC_TD_DELETED_DT = $deletedDt;
            $data->save();
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Deleted Data Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Delete Data Product/Area Success');
    }

    public function filter(Request $request)
    {
        // Validasi Data MAndatory
        try {
            $request->validate([
                'name' => ['string', 'max:100'],
                'biller_id' => ['string', 'max:255'],
                'groupname' => ['string', 'max:100'],
                'pan' => ['string', 'max:5'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;
        $biller = $request->biller_id;
        $groupName = $request->group_name;
        $pan = $request->pan;
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic get Data Filter
        try {
            $params = [
                'name' => $name,
                'biller' => $biller,
                'groupName' => $groupName,
                'pan' => $pan,
                'items' => $items
            ];

            $data = $this->productgetFilter($params, 'filter');
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Get Filter Data Product/Area Failed', $th->getMessage());
        }

        // Response Not Found
        if (false == $data) :
            return $this->productNotFound();
        endif;

        // add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->generalDataResponse(200, 'Filter Data Product/Area Success', $data);
    }

    public function trash(Request $request)
    {
        // Validasi Data MAndatory
        try {
            $request->validate([
                'name' => ['string', 'max:100'],
                'biller_id' => ['string', 'max:255'],
                'groupname' => ['string', 'max:100'],
                'pan' => ['string', 'max:5'],
                'items' => ['numeric', 'digits_between:1,8'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;
        $biller = $request->biller_id;
        $groupName = $request->group_name;
        $pan = $request->pan;
        $items = (null == $request->items) ? 10 : $request->items;

        // Logic get Data Trash
        try {
            $params = [
                'name' => $name,
                'biller' => $biller,
                'groupName' => $groupName,
                'pan' => $pan,
                'items' => $items
            ];

            $data = $this->productgetFilter($params, 'trash');
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Get Trash Data Product/Area Failed', $th->getMessage());
        }

        // Response Not Found
        if (false == $data) :
            return $this->productNotFound();
        endif;

        // add Index Number
        $data = $this->addIndexNumber($data);

        // Response Success
        return $this->generalDataResponse(200, 'Get List Trash Data Product/Area Success', $data);
    }

    public function getCount()
    {
        // Logic Get Data
        try {
            $data = ModelsTransactionDefinitionV2::getData()->get($this->productSimpleField());
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Get Count Data Total Product Failed', $th->getMessage());
        }

        // Hitung Jumlah Data
        $data = count($data);

        // Response Not Found
        if (null == $data) :
            return $this->generalResponse(404, 'Count Data Total Product Not Found');
        endif;

        // Response Success
        return $this->responseCustomKey(200, 'Get Count Total Product Success', 'total_product', $data);
    }

    public function restore(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate([
                'name' => ['required', 'array', 'min:1'],
                'name.*' => ['string', 'max:100'],
            ]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Data Variable
        $name = $request->name;
        $countName = count($name);
        $notFound = [];

        // Logic Get Trash data
        try {
            for ($i=0; $i < $countName; $i++) {
                $data[] = $this->productGetData($name[$i], 'trash');

                if (false == $data[$i]) :
                    $notFound[] = $name[$i];
                    unset($name[$i]);
                endif;
            }

            // Reordering & Recounting $name
            $name = array_values($name);
            $countName = count($name);
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Restore Data Product/Area Failed', $th->getMessage());
        }

        // Response Not Found
        if (null == $countName) :
            return $this->productNotFound();
        endif;

        // Logic Restore data
        try {
            for ($i=0; $i < $countName; $i++) {
                $data = $this->productGetData($name[$i], 'trash');

                $data->CSC_TD_DELETED_DT = null;
                $data->CSC_TD_DELETED_BY = null;
                $data->save();
            }
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Restore Data Product/Area Failed', $th->getMessage());
        }

        // Response Success
        if (null == $notFound) :
            return $this->generalResponse(200, 'Restore Data Product/Area Success');
        endif;

        // Response With Warning
        if (null != $notFound) :
            $response['product_not_found'] = $notFound;
            return $this->generalDataResponse(
                202,
                'Restore Data Product/Area Success But Some Data Not Found',
                $response
            );
        endif;
    }

    public function deleteData(Request $request)
    {
        // Validasi Data Mandatory
        try {
            $request->validate(['name' => ['required', 'string', 'max:100']]);
        } catch (ValidationException $th) {
            return $this->invalidValidation($th->validator->errors());
        }

        // Inisialisasi Variable
        $name = $request->name;

        // Check Data Product
        $data = ModelsTransactionDefinitionV2::where('CSC_TD_NAME', $name)->first();

        // Response Not Found
        if (false == $data) :
            return $this->productNotFound();
        endif;

        // Logic Delete Data
        try {
            ModelsTransactionDefinitionV2::where('CSC_TD_NAME', $name)->delete();
        } catch (\Throwable $th) {
            return $this->generalDataResponse(500, 'Delete Data Product/Area Failed', $th->getMessage());
        }

        // Response Success
        return $this->generalResponse(200, 'Delete Data Product/Area Success');
    }
}
