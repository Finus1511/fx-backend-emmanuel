<?php

namespace App\Http\Resources;

use App\Models\ChatAsset;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
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

        $chat_asset = ChatAsset::where('chat_message_id', $this->id)->first();

        $chat_asset = $chat_asset->file ? $chat_asset->makeVisible('file') : '';

        return [
            'id' => $this->id,
            'reference_id' => $this->reference_id,
            'from_user_id' => $this->from_user_id,
            'to_user_id' => $this->to_user_id,
            'admin_id' => $this->admin_id,
            'message' => $this->message,
            'is_file_upload' => $this->is_file_upload == YES ? YES : NO,
            'amount' => $this->amount,
            'token' => $this->token,
            'chat_assets' => $chat_asset,
            'status' => $this->status,
            'status_formatted' => $this->status ? tr('approved') : tr('declined'),
            'created_at' => common_date($this->created_at, $timezone),
            'updated_at' => common_date($this->updated_at, $timezone)
        ];
    }
}
