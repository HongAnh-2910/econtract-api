<?php

namespace App\Http\Resources\V1\Member;

use App\Enums\StatusIsActive;
use App\Http\Resources\V1\Department\DepartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class MemberResource extends JsonResource
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
            "name" => $this->name,
            "email" => $this->email,
            "img_user" => asset(config('pathUploadFile.path_avatar_user').'/'.$this->img_user),
            "provider" => $this->provider,
            "parent_id" => $this->parent_id,
            "active" => Arr::get(StatusIsActive::IS_ACTIVE, $this->active),
            "departments" => DepartmentResource::collection($this->whenLoaded('departments'))
        ];
    }
}
