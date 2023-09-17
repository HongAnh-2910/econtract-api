<?php

namespace App\Http\Resources\V1\Folder;

use App\Http\Resources\V1\Member\MemberResource;
use App\Http\Resources\V1\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'user_id'   => $this->user_id,
            'user'      => new UserResource($this->whenLoaded('user')),
            'slug'      => $this->slug,
            'parent'    => new FolderResource($this->whenLoaded('parent')),
            'create_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'created_at' => $this->created_at
        ];
    }
}
