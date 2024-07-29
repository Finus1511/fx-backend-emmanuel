<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
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
        
        return [

            'promo_code_id' => $this->id,
            'promo_code_unique_id' => $this->unique_id ?: '',
            'user_id' => $this->user_id ?: 0,
            'platform' => $this->platform ?: '',
            'promo_code' => $this->promo_code ?: '',
            'amount_type' => $this->amount_type ?: 0,
            'amount_type_formatted' => $this->amount_type ? tr('amount') : tr('percentage'),
            'amount' => $this->amount ?: 0.00,
            'no_of_users_limit' => $this->no_of_users_limit ?: 0,
            'per_users_limit' => $this->per_users_limit ?: 0,
            'start_date' => common_date($this->start_date, $timezone),
            'start_date_formatted' => common_date($this->start_date, $timezone, 'Y-m-d h:i'),
            'expiry_date' => common_date($this->expiry_date, $timezone),
            'expiry_date_formatted' => common_date($this->expiry_date, $timezone, 'Y-m-d h:i'),
            'status' => $this->status ?: 0,
            'status_formatted' => $this->status ? tr('active') : tr('inactive'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}