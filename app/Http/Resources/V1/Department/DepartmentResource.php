<?php

namespace App\Http\Resources\V1\Department;

use App\Http\Resources\V1\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'name' => $this->name,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'date_create' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'user' => new UserResource($this->whenLoaded('user')),
            'parent' => new DepartmentResource($this->whenLoaded('parent')),
            'children' => DepartmentResource::collection($this->whenLoaded('childrenDepartment'))
        ];
    }
}
