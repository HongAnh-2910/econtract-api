<?php

namespace App\Http\Resources\V1\Application;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DateTimeOfApplicationResource extends JsonResource
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
            'id'                => $this->id,
            'information_day_1' => $this->information_day_1,
            'information_day_2' => Carbon::parse($this->information_day_2)->format('Y-m-d'),
            'information_day_3' => $this->information_day_3,
            'information_day_4' => Carbon::parse($this->information_day_4)->format('Y-m-d'),
            'application_id'    => $this->application_id
        ];
    }
}
