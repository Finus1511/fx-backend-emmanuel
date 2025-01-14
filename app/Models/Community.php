<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $hidden = ['id','unique_id','file'];

    protected $appends = ['community_id', 'community_unique_id'];

    protected $guarded = ['id'];

    public function getCommunityIdAttribute() {

        return $this->id;
    }

    public function getCommunityUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function chatMessage() {

        return $this->belongsTo(CommunityMessage::class, 'community_message_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "C"."-".uniqid();

            $model->attributes['name'] = "Community"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "C"."-".$model->attributes['id']."-".uniqid();

            $model->attributes['name'] = "Community"."-".uniqid();

            $model->save();
        
        });

    }
}
