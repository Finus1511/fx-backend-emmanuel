<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStreamShopping extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {

        return $this->belongsTo(User::class)->withDefault();
    }

    public function lssPayment() {

        return $this->belongsTo(LssPayment::class, 'id', 'live_stream_shopping_id')->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "LSS-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "LSS-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}
