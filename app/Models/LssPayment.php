<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LssPayment extends Model
{
    use HasFactory;

   protected $guarded = ['id'];

    public function user() {

        return $this->belongsTo(User::class)->withDefault();
    }

    public function lssStreamShopping() {

        return $this->belongsTo(LiveStreamShopping::class, 'live_stream_shopping_id', 'id')->withDefault();
    }

   public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "LSSP-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "LSSP-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}