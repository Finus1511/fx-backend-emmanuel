<?php

namespace App\Http\Resources;

use App\Models\{User, VirtualExperienceFile};

use Illuminate\Http\Resources\Json\JsonResource;

class VirtualExperienceResource extends JsonResource
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
            'virtual_experience_id' => $this->id,
            'unique_id' => $this->unique_id,
            'user_id' => $this->user_id ? : '',
            'user_info' => $this->whenLoaded('user', [
                'user_id' => $this->user->id ?? "",
                'name' => $this->user->name ?? "",
                'username' => $this->user->username ?? "",
                'picture' => $this->user->picture ?? "",
            ], []),
            'files' => VirtualExperienceFile::where('virtual_experience_id', $this->id)->get(),
            'title' => $this->title ? : '',
            'description' => $this->description ? : '',
            'notes' => $this->notes ? : '',
            'price_per' => $this->price_per ? : '',
            'price_per_formatted' => formatted_amount($this->price_per ?? 0.00),
            'total_capacity' => $this->total_capacity ? : 0,
            'used_capacity' => $this->used_capacity ? : 0,
            'onhold_capacity' => $this->onhold_capacity ? : 0,
            'remaining_capacity' => $this->remaning_capacity ? : 0,
            'agora_token' => $this->agora_token ?: '',
            'virtual_id' => $this->virtual_id ?: '',
            'host_id' => $this->host_id ?: '',
            'snapshot' => asset('images/livestreaming_placeholder.jpg'),
            'scheduled_start' => common_date($this->scheduled_start, $timezone, 'Y-m-d H:i:s'),
            'start_time' => common_date($this->scheduled_start, $timezone, 'h:i A'),
            'end_time' => common_date($this->scheduled_end, $timezone, 'h:i A'),
            'start_date' => common_date($this->scheduled_start, $timezone, 'd M Y'),
            'end_date' => common_date($this->scheduled_end, $timezone, 'd M Y'),
            'scheduled_end' => common_date($this->scheduled_end, $timezone, 'Y-m-d H:i:s'),
            'payment_id' => $this->payment_id ? : $this->unique_id,
            'is_creator' => creator_check_formatted($this->user_id),
            'start_call' => start_call_formatted($this->scheduled_start, $this->scheduled_end, $timezone),
            'user_needs_to_pay' => is_creator_needs_to_pay($this->id, $request->id),
            'virtual_experience_creator' => virtual_experience_creator_check($this->id, $request->id),
            'actual_start' => common_date($this->actual_start, $timezone),
            'actual_end' => common_date($this->actual_end, $timezone),
            'status' => intval($this->status),
            'status_formatted' => virtual_experience_status_text_formatted($this->status),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        return $data;
    }
}
