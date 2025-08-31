<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'option_text'];

    public function options() {
        
        return $this->hasMany(PollOption::class);
    }

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
