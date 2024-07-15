<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Setting;

class ChatAsset extends Model
{
    protected $hidden = ['id','unique_id','file'];

	protected $appends = ['chat_asset_id', 'chat_asset_unique_id','amount_formatted','file_name'];

    protected $guarded = ['id'];

	public function getChatAssetIdAttribute() {

		return $this->id;
	}

    public function getAmountFormattedAttribute() {

        return formatted_amount(Setting::get('is_only_wallet_payment') ? $this->token :$this->amount);
    }

    public function getFileNameAttribute() {

        return basename($this->file);
    }

	public function getChatAssetUniqueIdAttribute() {

		return $this->unique_id;
	}

    public function chatMessage() {

        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return $query->select(
            'chat_assets.*',
            'chat_assets.file as asset_file',
        );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlurResponse($query) {

        return $query->select(
            'chat_assets.*',
            'chat_assets.blur_file as asset_file',
        );
    
    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CA"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CA"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
