<?php

namespace App\Http\Resources\V1\Signature;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "dataX" => $this->dataX,
            "dataY" => $this->dataY,
            "created_at" => Carbon::parse($this->created_at)->format('d-m-Y'),
            "contract_id" => $this->contract_id,
            "token" => $this->null,
            "sign_sequence" => $this->sign_sequence,
            "email" => $this->email,
            "name" => $this->name,
            "dataPage" => $this->dataPage,
            "signatured_at" => $this->signatured_at,
            "mailed_at" => $this->mailed_at,
            "type" => $this->type,
            "width" => $this->width,
            "height" => $this->height,
            "phone" => $this->phone
        ];
    }
}
