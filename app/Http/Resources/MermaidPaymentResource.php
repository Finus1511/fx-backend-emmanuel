<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class MermaidPaymentResource extends JsonResource
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
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'user_details' =>  UserPreviewResource::make($this->user),
            'mermaid_details' => MermaidResource::make($this->mermaid),
            'payment_id' => $this->payment_id ?: '',
            'payment_mode' => $this->payment_mode,
            'admin_amount' => $this->admin_amount ?: 0.00,
            'user_amount' => $this->user_amount ?: 0.00,
            'amount' => $this->amount ?: 0.00,
            'amount_formatted' => formatted_amount($this->amount),
            'currency' => $this->currency ?: '',
            'status' => $this->status,
            'status_formatted' => $this->status == PAID ? tr('paid'): tr('not_paid'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}