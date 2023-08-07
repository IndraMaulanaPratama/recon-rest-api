<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoreBankResource extends JsonResource
{

    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        CoreBankResource::$wrap = '';
        $this->status = $status;
        $this->message = $message;
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
            'result_data' => $this->resource,
        ];
    }
}
