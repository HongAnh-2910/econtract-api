<?php

namespace App\Http\Resources\V1;

use App\Enums\Status;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Http\Resources\V1\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (!is_null($this->type))
        {
            return [
                'file_id'           => $this->id,
                'name'              => $this->name,
                'path'              => $this->path,
                'type'              => $this->type,
                'image_type'        => checkExtensionFileGetImgType($this->type),
                'date_create'       => Carbon::parse($this->created_at)->format('d-m-Y'),
                'user_id'           => $this->user_id,
                'user'              => new UserResource($this->whenLoaded('user')),
                'shared_users'      => UserResource::collection($this->whenLoaded('users')),
                'folder_id'         => $this->folder_id,
                'folder'            => new FolderResource($this->whenLoaded('folder')),
                'size'              => $this->size,
                'upload_st'         => $this->upload_st,
                'contract_id'       => $this->contract_id,
                'file_soft_deleted' => $this->file_soft_deleted,
                'created_at'        => $this->created_at,
                'type_check'        => Status::FILE
            ];
        }else
        {
            return [
                'folder_id'    => $this->folder_id,
                'name'         => $this->name,
                'user_id'      => $this->user_id,
                'user'         => new UserResource($this->whenLoaded('user')),
                'shared_users' => UserResource::collection($this->whenLoaded('users')),
                'slug'         => $this->slug,
                'parent'       => new FolderResource($this->whenLoaded('parent')),
                'create_at'    => Carbon::parse($this->created_at)->format('d-m-Y'),
                'created_at'   => $this->created_at,
                'type_check'   => Status::FOLDER
            ];
        }
    }
}
