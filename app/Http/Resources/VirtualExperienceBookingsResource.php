<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VirtualExperienceBookingsResource extends JsonResource
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
            'virtual_experience_id' => $this->virtual_experience_id,
            'virtual_experience_user_id' => $this->virtual_experience_user_id,
            'user_id' => $this->user_id ? : '',
            'virtual_user_info' => $this->whenLoaded('virtualExperienceUser', [
                'user_id' => $this->virtualExperienceUser->id ?? "",
                'name' => $this->virtualExperienceUser->name ?? "",
                'username' => $this->virtualExperienceUser->username ?? "",
                'picture' => $this->virtualExperienceUser->picture ?? "",
            ], []),
            'user_info' => $this->whenLoaded('user', [
                'user_id' => $this->user->id ?? "",
                'name' => $this->user->name ?? "",
                'username' => $this->user->username ?? "",
                'picture' => $this->user->picture ?? "",
            ], []),

            'virtual_experience_info' => $this->whenLoaded('virtualExperience', [
                'user_id' => $this->virtualExperience->user_id ?? "",
                'unique_id' => $this->virtualExperience->unique_id ?? "",
                'scheduled_start' => common_date($this->virtualExperience->scheduled_start, $timezone, 'Y-m-d H:i:s'),
                'scheduled_end' => common_date($this->virtualExperience->scheduled_end, $timezone, 'Y-m-d H:i:s'),
                'start_date' => common_date($this->virtualExperience->scheduled_start, $timezone, 'd M Y'),
                'end_date' => common_date($this->virtualExperience->scheduled_end, $timezone, 'd M Y'),
                'start_time' => common_date($this->virtualExperience->scheduled_start, $timezone, 'h:i A'),
                'end_time' => common_date($this->virtualExperience->scheduled_end, $timezone, 'h:i A'),
                'title' => $this->virtualExperience->title ?? "",
            ], []),
            'payment_id' => $this->payment_id ? : '',
            'price_per' => formatted_amount($this->price_per),
            'total_capacity' => $this->total_capacity ? : '',
            'tax_amount' => $this->tax_amount ? : '',
            'commission_amount' => $this->commission_amount ? : '',
            'sub_total' => $this->sub_total ? : '',
            'user_needs_to_pay' => is_creator_needs_to_pay($this->virtual_experience_id, $request->id),
            'total' => $this->total ? : '',
            'payment_mode' => $this->payment_mode ? : '',
            'is_failed' => $this->is_failed,
            'failed_reason' => $this->failed_reason,
            'start' => common_date($this->start, $timezone),
            'end' => common_date($this->end, $timezone),
            'paid_date' => common_date($this->paid_date, $timezone),
            'status' => intval($this->status),
            'status_formatted' => virtual_experience_status_text_formatted($this->status),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        return $data;
    }
}
