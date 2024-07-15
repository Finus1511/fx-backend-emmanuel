<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizedProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function personalizedRequest()
    {
        return $this->belongsTo(PersonalizedRequest::class, 'personalized_request_id');
    }

    public function personalizedProductFiles()
    {
        return $this->hasMany(PersonalizedProductFile::class, 'personalized_product_id');
    }

    public function personalizedDeliveryAddress()
    {
        return $this->belongsTo(PersonalizedDeliveryAddress::class, 'personalized_delivery_address_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PP-" . uniqid();
        });

        static::created(function ($model) {
            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);
            $model->attributes['unique_id'] = "PP-" . $id;
            $model->save();
        });

        static::deleting(function ($model){
            $model->personalizedProductFiles()->delete();
        });
    }
}
