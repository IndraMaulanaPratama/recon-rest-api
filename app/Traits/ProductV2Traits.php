<?php

namespace App\Traits;

use App\Models\TransactionDefinitionV2;
use App\Traits\ResponseHandler;

use function PHPSTORM_META\type;

trait ProductV2Traits
{
    use ResponseHandler;

    // Inisialisasi Response Simple Field
    public function productSimpleField()
    {
        return [
            'CSC_TD_NAME AS NAME',
            'CSC_TD_BILLER_ID AS BILLER_ID',
            'CSC_TD_GROUPNAME AS GROUP_NAME',
            'CSC_TD_ALIASNAME AS ALIAS_NAME',
            'CSC_TD_DESC AS DESCRIPTION',
        ];
    }

    // Inisialisasi Response Detail
    public function productDetailField()
    {
        return [
            'CSC_TD_NAME AS NAME',
            'CSC_TD_BILLER_ID AS BILLER_ID',
            'CSC_TD_GROUPNAME AS GROUP_NAME',
            'CSC_TD_ALIASNAME AS ALIAS_NAME',
            'CSC_TD_DESC AS DESCRIPTION',
            'CSC_TD_FINDCRITERIA AS FIND_CRITERIA',
            'CSC_TD_PAN AS PAN',
            'CSC_TD_CREATED_DT AS CREATED',
            'CSC_TD_MODIFIED_DT AS MODIFIED',
            'CSC_TD_CREATED_BY AS CREATED_BY',
            'CSC_TD_MODIFIED_BY AS MODIFIED_BY',
        ];
    }

    // Inisialisasi Response Get Data Field
    public function productGetDataField()
    {
        return [
            'CSC_TD_NAME AS NAME',
            'CSC_TD_BILLER_ID AS BILLER_ID',
            'CSC_TD_GROUPNAME AS GROUP_NAME',
            'CSC_TD_ALIASNAME AS ALIAS_NAME',
            'CSC_TD_DESC AS DESCRIPTION',
            'CSC_TD_FINDCRITERIA AS FIND_CRITERIA',
            'CSC_TD_PAN AS PAN',
        ];
    }


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

    // Get Data Product
    public function productGetData($name, $type)
    {
        if ('data' == $type) :
            $data = TransactionDefinitionV2::searchData($name)->first($this->productGetDataField());
        elseif ('trash' == $type) :
            $data = TransactionDefinitionV2::searchTrashData($name)
            ->first([
                'CSC_TD_NAME',
                'CSC_TD_DELETED_DT',
                'CSC_TD_DELETED_BY',
            ]);
        endif;

        // null = false | !null = $data
        return (null != $data) ? $data : false;
    }

    // Cek Deleted Data Product
    public function productCheckDeletedData($name)
    {
        $data = TransactionDefinitionV2::searchTrashData($name)->first('CSC_TD_NAME AS NAME');

        // null = false | !null = $data
        return (null != $data) ? true : false;
    }

    // Get Data Filter Or Trash Data Product
    public function productgetFilter($params, $type)
    {
        // Logic Get Data
        $data = TransactionDefinitionV2::where(function ($query) use ($params, $type) {
            if (null != $params['name']) :
                $query->filterSearchData($params['name']);
            endif;

            if (null != $params['biller']) :
                $query->filterBiller($params['biller']);
            endif;

            if (null != $params['groupName']) :
                $query->filterGrouopName($params['grouopName']);
            endif;

            if (null != $params['pan']) :
                $query->filterPan($params['pan']);
            endif;

            if ($type == 'filter') :
                $query->getData();
            endif;

            if ($type == 'trash') :
                $query->getTrashData();
            endif;
        })
        ->paginate($params['items'], $this->productDetailField());

        // Hitung Jumlah Data
        $count = count($data);

        // Null == false | !Null == $data
        return (null == $count) ? false : $data;
    }
}
