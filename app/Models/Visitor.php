<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ip_address', 'country', 'country_code'];

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "V"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "V"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
