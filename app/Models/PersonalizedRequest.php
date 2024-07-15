<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizedRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function sender() {

       return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {

       return $this->belongsTo(User::class, 'receiver_id');
    }

    public function deliveryAddress() {

       return $this->belongsTo(PersonalizedDeliveryAddress::class, 'personalized_delivery_address_id');
    }


    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "PR-".uniqid();
        });

        static::created(function($model) {

            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);

            $model->attributes['unique_id'] = "PR-".$id;
            
            $model->save();

        });

    }
}
