<?php

namespace App\Http\Resources\V1\Application;

use App\Enums\ApplicationReason;
use App\Enums\ApplicationStatus;
use App\Http\Resources\V1\File\FileResource;
use App\Http\Resources\V1\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $day = 0;
        if (!is_null($this->whenLoaded('dateTimeApplications')))
        {
            foreach ($this->whenLoaded('dateTimeApplications') as $value)
            {
                $start = Carbon::createFromFormat('Y-m-d H:s:i' ,$value->information_day_2);
                $end = Carbon::createFromFormat('Y-m-d  H:s:i' ,$value->information_day_4);
                if ($value->information_day_1 == $value->information_day_3 && $start->diffInDays($end) == 0)
                {
                    $day+=0.5;
                }else
                {
                    $day+= $start->diffInDays($end) + 1;
                }

            }
        }
        return [
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $this->name,
            'status'                  => ApplicationStatus::getStatusApplication($this->status->getValue()),
            'reason'                  => $this->reason,
            'type_reason_application' => ApplicationReason::getApplicationReason($this->application_type),
            'department_id'           => $this->department_id,
            'position'                => $this->position,
            'day'                     => $day,
            'user'                    => new UserResource($this->whenLoaded('user')),
            'description'             => $this->description,
            'files'                   => FileResource::collection($this->whenLoaded('applicationFiles')),
            'created_at'              => Carbon::parse($this->created_at)->format('Y-m-d'),
            'user_create'             => new UserResource($this->whenLoaded('userCreateApplication')),
            'type_create'             => ApplicationStatus::getApplicationByKey($this->type)
        ];
    }
}
