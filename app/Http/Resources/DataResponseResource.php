<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataResponseResource extends JsonResource
{
    public function __construct($status, $message, $resource)
    {
        DataResponseResource::$wrap = '';

        $this->status = $status;
        $this->message = $message;
        $this->resource = $resource;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'result_code' => $this->status,
            'result_message' => $this->message,
            'result_data' => $this->resource
        ];
    }
}
