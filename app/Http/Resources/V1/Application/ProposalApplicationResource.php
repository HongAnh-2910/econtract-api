<?php

namespace App\Http\Resources\V1\Application;

use App\Enums\ApplicationProposal;
use App\Enums\ApplicationReason;
use App\Enums\ApplicationStatus;
use App\Http\Resources\V1\File\FileResource;
use App\Http\Resources\V1\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProposalApplicationResource extends JsonResource
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
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $this->name,
            'proposal_name'           => $this->proposal_name,
            'proponent'               => $this->proponent,
            'price_proposal'          => $this->price_proposal,
            'account_information'     => $this->account_information,
            'delivery_time'           => $this->delivery_time,
            'delivery_date'           => Carbon::parse($this->delivery_date)->format('Y-m-d'),
            'status'                  => ApplicationStatus::getStatusApplication($this->status->getValue()),
            'type_reason_application' => ApplicationProposal::getApplicationProposalReason($this->application_type),
            'department_id'           => $this->department_id,
            'position'                => $this->position,
            'user'                    => new UserResource($this->whenLoaded('user')),
            'user_follow'             => UserResource::collection($this->whenLoaded('users')),
            'files'                   => FileResource::collection($this->whenLoaded('applicationFiles')),
            'created_at'              => Carbon::parse($this->created_at)->format('Y-m-d'),
            'user_create'             => new UserResource($this->whenLoaded('userCreateApplication')),
            'type_create'             => ApplicationStatus::getApplicationByKey($this->type)
        ];
    }
}
