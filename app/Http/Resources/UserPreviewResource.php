<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $timezone = $request->timezone;
        
        return [

            'user_id' => $this->id,
            'user_unique_id' => $this->unique_id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'picture' => $this->picture ?: asset('images/placeholder.jpeg'),
            'cover' => $this->cover ?: asset('images/placeholder.jpeg'),
            'about' => $this->about,
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}
