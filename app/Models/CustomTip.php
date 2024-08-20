<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTip extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'unique_id'];

    public function getCustomTipIdAttribute() {

        return $this->id;
    }

    public function getCustomTipUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function scopeApproved($query) {

        return $query->where('status', APPROVED);

    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CT"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CT"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

        });

    }
}
