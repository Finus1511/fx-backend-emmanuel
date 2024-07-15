<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProductResource extends JsonResource
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

            'user_product_id' => $this->id,
            'user_product_unique_id' => $this->unique_id,
            'user_id' => $this->user_id,
            'product_category_id' => $this->product_category_id,
            'product_sub_category_id' => $this->product_sub_category_id,
            'name' => $this->name ?: '',
            'product_category' => $this->whenLoaded('productCategory'),
            'product_sub_category' => $this->whenLoaded('productSubCategory'),
            'description' => $this->description ?: '',
            'picture' => $this->picture ?: asset('images/placeholder.jpeg'),
            'quantity' => $this->quantity ?: 0,
            'token' => $this->token ?: 0.00,
            'price' => $this->price ?: 0.00,
            'delivery_price' => $this->delivery_price ?: 0.00,
            'is_outofstock' => $this->is_outofstock ? tr('yes') :tr('no'),
            'is_visible' => $this->is_visible ? tr('yes') :tr('no'),
            'is_live_stream_product' => $this->is_live_stream_product ? YES : NO,
            'is_digital_product' => $this->is_digital_product ? YES : NO,
            'is_need_to_pay' => user_need_to_pay($request->id, $this->id),
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}
