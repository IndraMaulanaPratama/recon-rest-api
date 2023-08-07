<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResponseResource extends JsonResource
{
    public function __construct($status, $message, $config, $resource)
    {
        parent::__construct($resource);
        CoreDownCentralResource::$wrap = '';
        $this->status = $status;
        $this->message = $message;
        $this->config = $config;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            'result_code' => $this->status,
            'result_message' => $this->message,
            'config' => $this->config,
            'result_data' => $this->resource,
        ];
    }
}
