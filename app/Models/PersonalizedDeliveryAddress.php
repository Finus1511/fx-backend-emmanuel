<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizedDeliveryAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function personalizedRequest() {

       return $this->belongsTo(PersonalizedRequest::class, 'personalized_request_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "PDA-".uniqid();
        });

        static::created(function($model) {

            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);

            $model->attributes['unique_id'] = "PDA-".$id;
            
            $model->save();

        });

    }
}

