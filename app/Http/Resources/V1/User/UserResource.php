<?php

namespace App\Http\Resources\V1\User;

use App\Enums\StatusIsActive;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $checkNameRoute = $request->route()->getName() == 'register' || $request->route()->getName() == 'login';
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "img_user" => $this->img_user,
            "provider" => $this->provider,
            "parent_id" => $this->parent_id,
            "active" => Arr::get(StatusIsActive::IS_ACTIVE, $this->active),
            "access_token" => $this->when($checkNameRoute,data_get($this ,'access_token')),
            "token_type" => data_get($this ,'token_type')
        ];
    }
}
