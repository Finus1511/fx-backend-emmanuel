<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionFileResource extends JsonResource
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
            'collection_id' => $this->collection_id,
            'file' => $this->file ?: '',
            'file_type' => $this->file_type ?: '',
            'preview_file' => $this->preview_file,
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}
