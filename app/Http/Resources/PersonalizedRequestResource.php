<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonalizedRequestResource extends JsonResource
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

        $data = [
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'type' => $this->type,
            'product_type' => intval($this->product_type),
            'product_type_formatted' => product_type_formatted($this->product_type),
            'amount' => $this->amount ?: 0.00,
            'file' => $this->file ?: '',
            'preview_file' => $this->preview_file ?: asset('images/placeholder.jpeg'),
            'file_type' => $this->file_type ?: '',
            'amount_formatted' => formatted_amount($this->amount),
            'description' => $this->description ?: '',
            'cancel_reason' => $this->cancel_reason ?: '',
            'is_amount_update' => $this->is_amount_update ? YES : NO,
            'status' => $this->status,
            'status_formatted' => personalize_status_formatted($this->status),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        if ($this->sender_id) {
            $data['sender'] = [
                'id' => $this->sender->id ?? '', 
                'name' => $this->sender->name ?? '', 
                'email' => $this->sender->email ?? '', 
                'picture' => $this->sender->picture ?? '',

            ];
        }

        if ($this->receiver_id) {

            $data['receiver'] = [
                'id' => $this->receiver->id ?? '', 
                'name' => $this->receiver->name ?? '',
                'email' => $this->receiver->email ?? '',
                'picture' => $this->receiver->picture ?? '', 
            ];
        }

        if ($this->personalized_delivery_address_id) {

            $data['delivery_address'] = [
                'id' => $this->deliveryAddress->id ?? '', 
                'name' => $this->deliveryAddress->name ?? '',
                'address' => $this->deliveryAddress->address ?? '',
                'pincode' => $this->deliveryAddress->pincode ?? '', 
                'city' => $this->deliveryAddress->city ?? '', 
                'state' => $this->deliveryAddress->state ?? '', 
                'country' => $this->deliveryAddress->country ?? '', 
                'country_code' => $this->deliveryAddress->country_code ?? '', 
                'landmark' => $this->deliveryAddress->landmark ?? '', 
                'contact_number' => $this->deliveryAddress->contact_number ?? '', 
            ];
        }

        return $data;
    }
}
