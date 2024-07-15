<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualExperienceBooking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {

        return $this->belongsTo(User::class)->withDefault();
    }

    public function virtualExperienceUser() {

        return $this->belongsTo(User::class, 'virtual_experience_user_id')->withDefault();
    }

    public function virtualExperience() {

        return $this->belongsTo(VirtualExperience::class)->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "VEB-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VEB-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}
