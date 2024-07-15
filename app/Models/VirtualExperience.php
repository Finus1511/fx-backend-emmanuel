<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class VirtualExperience extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['virtual_experience_id', 'virtual_experience_unique_id'];

    protected $guarded = ['id'];

    public function getVirtualExperienceIdAttribute() {

        return $this->id;
    }

    public function getVirtualExperienceUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function user() {

       return $this->belongsTo(User::class, 'user_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = uniqid();

        });
        
        static::created(function ($model) {

            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);

            $model->attributes['unique_id'] = "VT-".$id;
            
            $model->save();
            
        });

    }
}
