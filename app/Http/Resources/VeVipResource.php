<?php

namespace App\Http\Resources;

use App\Models\{User, VeVipFile};

use Illuminate\Http\Resources\Json\JsonResource;

class VeVipResource extends JsonResource
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
            'files' => VeVipFile::where('ve_vip_id', $this->id)->get(),
            'title' => $this->title ? : '',
            'description' => $this->description ? : '',
            'notes' => $this->notes ? : '',
            'amount' => $this->amount ? : '',
            'amount_formatted' => formatted_amount($this->amount ?? 0.00),
            'latitude' => $this->latitude ? : 0.000,
            'longitude' => $this->longitude ? : 0.000,
            'location' => $this->location ? : "",
            'snapshot' => asset('images/livestreaming_placeholder.jpg'),
            'scheduled_date' => common_date($this->scheduled_date, $timezone, 'd M Y'),
            'payment_id' => $this->payment_id ? : $this->unique_id,
            'is_creator' => creator_check_formatted($this->user_id),
            'user_needs_to_pay' => is_creator_needs_to_pay_for_vip_ve($this->id, $request->id),
            'virtual_experience_creator' => virtual_experience_vip_creator_check($this->id, $request->id),
            'status' => intval($this->status),
            'status_formatted' => virtual_experience_status_text_formatted($this->status),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        return $data;
    }
}
