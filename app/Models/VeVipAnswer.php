<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeVipAnswer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function virtualExperience() {

        return $this->belongsTo(VeVip::class)->withDefault();
    }

    public function question() {

        return $this->belongsTo(VeVipQuestion::class, 've_vip_question_id')->withDefault();
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "VEVA-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VEVA-".$model->attributes['id']."-".uniqid();
            
            $model->save();

        });

        static::deleting(function ($model) {

        });
    }
}
