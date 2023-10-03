<?php

namespace App\Http\Resources\V1\File;

use App\Http\Resources\V1\Folder\FolderResource;
use App\Http\Resources\V1\User\UserResource;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'path'              => $this->path,
            'type'              => $this->type,
            'image_type'        => checkExtensionFileGetImgType($this->type),
            'link_file'         => asset(config('pathUploadFile.path_file').'/'.$this->name),
            'date_create'       => Carbon::parse($this->created_at)->format('d-m-Y'),
            'user_id'           => $this->user_id,
            'user'              => new UserResource($this->whenLoaded('user')),
            'folder_id'         => $this->folder_id,
            'folder'            => new FolderResource($this->whenLoaded('folder')),
            'size'              => $this->size,
            'upload_st'         => $this->upload_st,
            'contract_id'       => $this->contract_id,
            'file_soft_deleted' => $this->file_soft_deleted,
            'created_at'        => $this->created_at
        ];
    }
}
