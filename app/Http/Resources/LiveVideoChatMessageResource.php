<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveVideoChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $timezone = $request->timezone ?? DEFAULT_TIMEZONE;
        return array_merge(parent::toArray($request), [
            'formatted_created_at' => common_date($this->created_at, $timezone),
        ]);
    }
}
