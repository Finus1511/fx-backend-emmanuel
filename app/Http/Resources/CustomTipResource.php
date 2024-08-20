<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomTipResource extends JsonResource
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

            'custom_tip_id' => $this->id,
            'custom_tip_unique_id' => $this->unique_id,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'amount_formatted' => formatted_amount($this->amount),
            'picture' => $this->picture ?: asset('images/placeholder.jpeg'),
            'type' => $this->type,
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}
