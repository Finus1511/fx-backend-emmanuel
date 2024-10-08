<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CollectionFileResource;

class CollectionResource extends JsonResource
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

        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'unique_id' => $this->unique_id,
            'name' => $this->name,
            'thumbnail' => $this->thumbnail ?: asset('images/placeholder.jpeg'),
            'amount' => $this->amount,
            'description' => $this->description,
            'is_paid' => $this->is_paid,
            'user_needs_to_pay' => collection_post_user_needs_to_pay($this->id, $request->id),
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];

        // if ($data['user_needs_to_pay'] == NO) {

            $data['collection_files_count'] = count($this->collectionFiles);
        // }

        return $data;
    }
}
