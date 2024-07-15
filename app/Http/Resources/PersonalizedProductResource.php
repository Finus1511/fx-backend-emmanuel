<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonalizedProductResource extends JsonResource
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
            'unique_id' => $this->unique_id ?: '',
            'name' => $this->name ?: '',
            'description' => $this->description ?: '',
            'shipping_url' => $this->shipping_url ?: '',
            'status' => $this->status,
            'status_formatted' => status_formatted($this->status),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        if ($this->personalized_request_id) {
            $data['personalized_request'] = [
                'id' => $this->personalizedRequest->id ?? '', 
                'unique_id' => $this->personalizedRequest->unique_id ?? '', 
                'sender_id' => $this->personalizedRequest->sender_id ?? '', 
                'receiver_id' => $this->personalizedRequest->receiver_id ?? '',
                'type' => $this->personalizedRequest->type ?? '',
                'product_type' => $this->personalizedRequest->product_type ?? '',
                'product_type_formatted' => product_type_formatted($this->personalizedRequest->product_type),
                'status' => $this->personalizedRequest->status ?? 0,
                'status_formatted' => personalize_status_formatted($this->personalizedRequest->status),
            ];
        }

        $personalized_product_files = $this->personalizedProductFiles; 

        $files = [];

        foreach ($personalized_product_files as $file) {

            $files[] = [

                'id' => $file->id ?: '',
                'unique_id' => $file->unique_id ?: '',
                'file' => $file->file ?: '',
                'file_type' => $file->file_type ?: '',

            ];
        }

        $data['personalized_product_files'] = $files;

        if ($this->personalized_delivery_address_id) {

            $data['delivery_address'] = [
                'id' => $this->personalizedDeliveryAddress->id ?? '', 
                'name' => $this->personalizedDeliveryAddress->name ?? '',
                'address' => $this->personalizedDeliveryAddress->address ?? '',
                'pincode' => $this->personalizedDeliveryAddress->pincode ?? '', 
                'city' => $this->personalizedDeliveryAddress->city ?? '', 
                'state' => $this->personalizedDeliveryAddress->state ?? '', 
                'country' => $this->personalizedDeliveryAddress->country ?? '', 
                'landmark' => $this->personalizedDeliveryAddress->landmark ?? '', 
                'contact_number' => $this->personalizedDeliveryAddress->contact_number ?? '', 
            ];
        }

        return $data;
    }
}
