<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\{LssProduct, UserProduct};

use App\Http\Resources\{UserProductResource};

class LiveStreamResource extends JsonResource
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
            'title' => $this->title ?: '',
            'stream_type' => $this->stream_type ?: '',
            'payment_type' => $this->payment_type ?: '',
            'description' => $this->description ?: '',
            'is_streaming' => $this->is_streaming ?: 0,
            'is_streaming' => $this->is_streaming == YES ? tr('yes') : tr('no'),
            'amount' => $this->amount ?: 0.00,
            'overall_revenue' => get_overall_revenue($this->id, $request->id),
            'amount_formatted' => formatted_amount($this->amount),
            'agora_token' => $this->agora_token ?: '',
            'virtual_id' => $this->virtual_id ?: '',
            'schedule_type' => $this->schedule_type ?: 0,
            'schedule_time' => common_date($this->schedule_time, $timezone ?? DEFAULT_TIMEZONE),
            'preview_file' => $this->preview_file ?: asset('images/livestream-placeholder.jpeg'),
            'preview_file_type' => $this->preview_file_type ?: '',
            'description' => $this->description ?: '',
            'start_time' => common_date($this->start_time, $timezone ?? DEFAULT_TIMEZONE),
            'end_time' => common_date($this->end_time, $timezone ?? DEFAULT_TIMEZONE),
            'status' => $this->status,
            'viewer_count' => $this->viewer_count ?: 0,
            'call_status' => check_call_status($this->id, $this->user_id, $this->schedule_time, $timezone ?? DEFAULT_TIMEZONE),
            'status_formatted' => live_stream_status_formatted($this->status),
            'is_user_needs_to_pay' => $request->id == $this->user_id || $this->payment_type == PAYMENT_TYPE_FREE ? NO : check_user_needs_to_pay($this->id, $request->id),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        if($this->user_id) {
            $data['user_details'] = [
                'id' => $this->user->id ?? '', 
                'name' => $this->user->name ?? '', 
                'email' => $this->user->email ?? '', 
                'picture' => $this->user->picture ?? '',
                'username' => $this->user->username ?? '',
            ];
        }

        return $data;
    }
}
