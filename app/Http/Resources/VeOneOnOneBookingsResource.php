<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VeOneOnOneBookingsResource extends JsonResource
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
            've_one_on_one_id' => $this->ve_one_on_one_id,
            've_one_on_one_user_id' => $this->ve_one_on_one_user_id,
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
                'scheduled_date' => common_date($this->virtualExperience->scheduled_date, $timezone, 'Y-m-d'),
                'title' => $this->virtualExperience->title ?? "",
                'location' => $this->virtualExperience->location ?? "",
                'scheduled_date' => $this->virtualExperience->scheduled_date ? common_date($this->virtualExperience->scheduled_date, $timezone, 'd M Y') : "",
                'status_formatted' => virtual_experience_status_text_formatted($this->virtualExperience->status ?? ""),
            ], []),
            'payment_id' => $this->payment_id ? : '',
            'amount' => formatted_amount($this->amount),
            'tax_amount' => $this->tax_amount ? : '',
            'commission_amount' => $this->commission_amount ? : '',
            'sub_total' => $this->sub_total ? : '',
            'total' => $this->total ? : '',
            'payment_mode' => $this->payment_mode ? : '',
            'is_failed' => $this->is_failed,
            'failed_reason' => $this->failed_reason,
            'paid_date' => common_date($this->paid_date, $timezone),
            'status' => intval($this->status),
            'status_formatted' => virtual_experience_status_text_formatted($this->status),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        return $data;
    }
}
