<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityUser extends Model
{
    use HasFactory;

    protected $hidden = ['id','unique_id','file'];

    protected $appends = ['community_user_id', 'community_user_unique_id'];

    protected $guarded = ['id'];

    public function getCommunityUserIdAttribute() {

        return $this->id;
    }

    public function getCommunityUserUniqueIdAttribute() {

        return $this->unique_id;
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "CU"."-".uniqid();

        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CU"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
