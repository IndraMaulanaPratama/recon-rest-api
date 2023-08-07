<?php

namespace App\Http\Controllers\Api\Tools;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\CoreBiller;
use App\Models\CoreGroupOfProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    public function getModul()
    {
        $data = CoreGroupOfProduct::distinct()->get(
            [
                'CSC_GOP_PRODUCT_PARENT_PRODUCT as MODUL',
            ]
        );

        if (null != count($data)) {
            return $data;
        } else {
            return response(
                new ResponseResource(404, 'Data Group Of Product Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function getBiller(Request $request)
    {
        $data = DB::connection('server_report')->table('CSCCORE_BILLER AS BILLER')
        ->distinct()
        ->join(
            'CSCCORE_GROUP_OF_PRODUCT AS GROUP_PRODUCT',
            'BILLER.CSC_BILLER_GROUP_PRODUCT',
            '=',
            'GROUP_PRODUCT.CSC_GOP_PRODUCT_GROUP'
        )
        ->where(function ($query) use ($request) {
            if (null != $request->modul) {
                $query->where('GROUP_PRODUCT.CSC_GOP_PRODUCT_PARENT_PRODUCT', $request->modul);
            }
        })->get('BILLER.CSC_BILLER_GROUP_PRODUCT AS BILLER');

        if (null != count($data)) {
            return $data;
        } else {
            return response(
                new ResponseResource(404, 'Data Biller Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }
    }

    public function getTransferFunds(Request $request)
    {
        $data = DB::connection('server_report')->table('CSCCORE_GROUP_TRANSFER_FUNDS AS TRANSFER_FUNDS')
        ->distinct()
        ->join(
            'CSCCORE_PRODUCT_FUNDS AS PRODUCT_FUNDS',
            'TRANSFER_FUNDS.CSC_GTF_ID',
            '=',
            'PRODUCT_FUNDS.CSC_PF_GROUP_TRANSFER'
        )
        ->join(
            'CSCCORE_TRANSACTION_DEFINITION AS TRANSACTION_DEFINITION',
            'PRODUCT_FUNDS.CSC_PF_PRODUCT',
            '=',
            'TRANSACTION_DEFINITION.CSC_TD_NAME'
        )
        ->join(
            'CSCCORE_BILLER_PRODUCT AS BILLER_PRODUCT',
            'TRANSACTION_DEFINITION.CSC_TD_NAME',
            '=',
            'BILLER_PRODUCT.CSC_BP_PRODUCT'
        )
        ->join(
            'CSCCORE_BILLER AS BILLER',
            'BILLER_PRODUCT.CSC_BP_BILLER',
            '=',
            'BILLER.CSC_BILLER_ID'
        )
        ->where(function ($query) use ($request) {
            if (null != $request->biller) {
                $query->where('BILLER.CSC_BILLER_GROUP_PRODUCT', $request->biller);
            }
        })->get(['TRANSFER_FUNDS.CSC_GTF_ID AS ID', 'TRANSFER_FUNDS.CSC_GTF_NAME AS GROUP_TRANSFER_FUNDS']);

        if (null != count($data)) {
            return $data;
        } else {
            return response(
                new ResponseResource(404, 'Data Group Transfer Funds Not Found'),
                404,
                ['Accept' => 'Application/json']
            );
        }
    }
}
