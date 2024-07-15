<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class LssDeliveryAddressResource extends JsonResource
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
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'name' => $this->name,
            'address' => $this->address,
            'pincode' => $this->pincode,
            'city' => $this->city,
            'state' => $this->state,
            'region_code' => $this->region_code,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'landmark' => $this->landmark,
            'contact_number' => $this->contact_number,
            'is_default' => $this->is_default,
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}
