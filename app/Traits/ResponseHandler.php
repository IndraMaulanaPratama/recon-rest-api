<?php

namespace App\Traits;

trait ResponseHandler
{
    public function generalResponse($status, $message)
    {
        return response()->json(
            [
              'result_code' => $status,
              'result_message' => $message
            ],
            $status
        );
    }

    public function generalDataResponse($status, $message, $data)
    {
        return response()->json(
            [
              'result_code' => $status,
              'result_message' => $message,
              'result_data' => $data
            ],
            $status
        );
    }

    public function generalConfigResponse($status, $message, $config, $data)
    {
        return response()->json(
            [
                'result_code' => $status,
                'result_message' => $message,
                'config' => $config,
                'result_data' => $data,
            ],
            $status
        );
    }

    public function responseSummary($status, $message, $summary, $data)
    {
        return response()->json(
            [
                'result_code' => $status,
                'result_message' => $message,
                'summary_data' => $summary,
                'result_data' => $data,
            ],
            $status
        );
    }

    public function responseCustomKey($status, $message, $key, $data)
    {
        return response()->json(
            [
                'result_code' => $status,
                'result_message' => $message,
                $key => $data,
            ],
            $status
        );
    }


    public function invalidValidation(...$data)
    {
        // Tidak ada data
        if (null == $data) :
            return response()->json(
                [
                    'result_code' => 400,
                    'result_message' => 'Invalid Data Validation',
                ],
                400
            );
        endif;

        // Disertai Data
        if (null != $data) :
            return response()->json(
                [
                    'result_code' => 400,
                    'result_message' => 'Invalid Data Validation',
                    'result_data' => $data[0],
                ],
                400
            );
        endif;
    }

    public function responseNotFound($message)
    {
        return response()->json(
            [
                'result_code' => 404,
                'result_message' => $message,
            ],
            404
        );
    }

    public function responseFailed($message)
    {
        return response()->json(
            [
                'result_code' => 500,
                'result_message' => $message,
            ],
            500
        );
    }

    public function responseDataFailed($message, $errors)
    {
        return response()->json(
            [
                'result_code' => 500,
                'result_message' => $message,
                'result_data' => $errors
            ],
            500
        );
    }

    public function responseUnprocessable()
    {
        return response()->json(
            [
              'result_code' => 422,
              'result_message' => 'Unprocessable Entity',
            ],
            422
        );
    }
}
