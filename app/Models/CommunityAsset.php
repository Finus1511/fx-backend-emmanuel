<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityAsset extends Model
{
    use HasFactory;

    protected $hidden = ['id','unique_id','file'];

    protected $appends = ['community_asset_id', 'community_asset_unique_id', 'file_name', 'asset_file', 'amount', 'is_user_paid', 'blur_file'];

    protected $guarded = ['id'];

    public function getCommunityAssetIdAttribute() {

        return $this->id;
    }

    public function getCommunityAssetUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getFileNameAttribute() {

        return basename($this->file);
    }

    public function getAssetFileAttribute() {

        return $this->file;
    }

    public function getBlurFileAttribute() {

        return asset('placeholder.jpeg');
    }

    public function getAmountAttribute() {

        return 0;
    }

    public function getIsUserPaidAttribute() {

        return 1;
    }

    public function chatMessage() {

        return $this->belongsTo(CommunityMessage::class, 'community_message_id');
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
