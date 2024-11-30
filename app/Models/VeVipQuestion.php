<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeVipQuestion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function virtualExperience() {

        return $this->belongsTo(VeVip::class)->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "VEVQ-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VEVQ-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}
