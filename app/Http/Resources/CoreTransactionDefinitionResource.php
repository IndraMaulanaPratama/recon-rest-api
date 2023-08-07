<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoreTransactionDefinitionResource extends JsonResource
{
    private $status;
    private $message;
    private $config;

    public function __construct($status, $message, $rconfig, $resource)
    {
        // parent::__construct($resource);
        CoreTransactionDefinitionResource::$wrap = '';
        $this->status = $status;
        $this->message = $message;
        $this->config = $rconfig;
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
        return  [
            'result_code' => $this->status,
            'result_message' => $this->message,
            'config' => $this->config,
            'result_data' => $this->resource,
        ];
    }
}
