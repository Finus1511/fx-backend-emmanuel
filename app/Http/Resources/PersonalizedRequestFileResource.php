<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonalizedRequestFileResource extends JsonResource
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
            
            'file' => $this->file ?: '',
            'amount' => $this->amount ? formatted_amount($this->amount) : 0.00,
            'file_type' => $this->file_type ?: '',
        ];
    }
}
