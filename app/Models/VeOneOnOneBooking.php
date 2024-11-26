<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeOneOnOneBooking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {

        return $this->belongsTo(User::class)->withDefault();
    }

    public function virtualExperienceUser() {

        return $this->belongsTo(User::class, 've_one_on_one_user_id')->withDefault();
    }

    public function virtualExperience() {

        return $this->belongsTo(VeOneOnOne::class, 've_one_on_one_id')->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "VEONOB-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VEONOB-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}
