<?php

namespace App\Http\Resources\V1\Contract;

use App\Enums\ContractStatus;
use App\Http\Resources\V1\Banking\BankingResource;
use App\Http\Resources\V1\File\FileResource;
use App\Http\Resources\V1\Follow\FollowResource;
use App\Http\Resources\V1\Signature\SignatureResource;
use App\Http\Resources\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $contract = parent::toArray($request);
        $contract['banking'] = new BankingResource($this->whenLoaded('banking'));
        $contract['payment'] = ContractStatus::getPayment($this->payments);
        $contract['user'] = new UserResource($this->whenLoaded('user'));
        $contract['files'] = FileResource::collection($this->whenLoaded('files'));
        $contract['signatures'] = SignatureResource::collection($this->whenLoaded('signatures'));
        $contract['follows'] = FollowResource::collection($this->whenLoaded('follows'));
        return $contract;
    }
}
