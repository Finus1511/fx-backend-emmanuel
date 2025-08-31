<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'poll_option_id', 'user_id'];

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PV"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PV"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
