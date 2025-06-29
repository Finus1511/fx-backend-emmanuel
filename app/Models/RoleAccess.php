<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleAccess extends Model
{
    use HasFactory;

    protected $hidden = ['id'];

    protected $fillable = ['admin_id', 'roles'];

    public function admin() {

       return $this->belongsTo(Admin::class, 'admin_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "RA"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "RA"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {
        
        });

    }
}
