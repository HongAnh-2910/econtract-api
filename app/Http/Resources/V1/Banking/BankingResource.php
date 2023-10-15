<?php

namespace App\Http\Resources\V1\Banking;

use Illuminate\Http\Resources\Json\JsonResource;

class BankingResource extends JsonResource
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
            'id' => $this->id,
            'en_name' => $this->en_name,
            'vn_name' => $this->vn_name,
            'short_name' => $this->shortName
        ];
    }
}
