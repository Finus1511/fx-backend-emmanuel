<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\{LiveStreamShopping};

class LiveStreamPaymentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'live_stream_shopping_id' => $this->live_stream_shopping_id ?: '',
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

        if($this->user_id) {
            $data['user_details'] = [
                'id' => $this->user->id ?? '', 
                'name' => $this->user->name ?? '', 
                'username' => $this->user->username ?? '',
                'email' => $this->user->email ?? '', 
                'picture' => $this->user->picture ?? '',
            ];
        }

        if($this->live_stream_shopping_id){

            $live_stream_shopping = LiveStreamShopping::firstWhere('id', $this->live_stream_shopping_id);

            $data['live_stream_shopping_details'] = new LiveStreamResource($live_stream_shopping->refresh());

        }

        return $data;
    }
}
