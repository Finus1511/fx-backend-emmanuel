<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LssProductPayment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {

        return $this->belongsTo(User::class)->withDefault();
    }

    public function userProduct() {

        return $this->belongsTo(UserProduct::class)->withDefault();
    }

    public function liveStreamShopping() {

        return $this->belongsTo(LiveStreamShopping::class, 'live_stream_shopping_id')->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "LSSPP-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "LSSPP-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}