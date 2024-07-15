<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizedProductFile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pesonalizeProduct() {

        return $this->belongsTo(PersonalizedProduct::class)->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "PPF-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PPF-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}
