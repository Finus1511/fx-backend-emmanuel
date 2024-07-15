<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LssDeliveryAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {

       return $this->belongsTo(User::class)->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "LSSDA-".uniqid();
        });

        static::created(function($model) {

            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);

            $model->attributes['unique_id'] = "LSSDA-".$id;
            
            $model->save();

        });

    }
}
