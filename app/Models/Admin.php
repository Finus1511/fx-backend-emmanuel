<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Helpers\Helper;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    /**
      * The attributes that are mass assignable.
      *
      * @var array
      */
    protected $fillable = [
        'name', 'email', 'password',
    ];
     /**
      * The attributes that should be hidden for arrays.
      *
      * @var array
      */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['admin_id', 'admin_unique_id', 'roles'];

    public function getAdminIdAttribute() {

        return $this->id;
    }

    public function getAdminUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getRolesAttribute() {

        $roles = $this->roleAccess->roles ?? "";

        unset($this->roleAccess);
        
        return $roles;
    }

    public function roleAccess() {

        return $this->hasOne(RoleAccess::class, 'admin_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "A"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "A"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {
        
            Helper::storage_delete_file($model->picture, COMMON_FILE_PATH);

        });

    }
}
