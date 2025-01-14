<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityMessage extends Model
{
    use HasFactory;

    protected $hidden = ['id','unique_id'];

    protected $appends = ['community_message_id', 'community_message_unique_id', 'chat_message_reference_id', 'from_username', 'from_displayname', 'from_userpicture', 'from_user_unique_id'];

    protected $guarded =['id'];

    public function getCommunityMessageIdAttribute() {

        return $this->id;
    }

    public function getCommunityMessageUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getChatMessageReferenceIdAttribute() {

        return $this->reference_id;
    }

    public function getFromUsernameAttribute() {

        $name = $this->fromUser->username ?? tr('n_a');

        unset($this->fromUser);

        return $name;
    }

    public function getFromUserPictureAttribute() {

        $image = $this->fromUser->picture ?? asset('placeholder.jpeg');

        unset($this->fromUser);

        return $image;

    }

    public function getFromDisplaynameAttribute() {

        $name = $this->fromUser->name ?? tr('n_a');

        unset($this->fromUser);

        return $name;

    }

    public function getFromUserUniqueIdAttribute() {

        $unique_id = $this->fromUser->unique_id ?? '';

        unset($this->fromUser);

        return $unique_id;

    }

    public function fromUser() {

       return $this->belongsTo(User::class, 'from_user_id');
    }

    public function chatAssets() {

       return $this->hasMany(CommunityAsset::class, 'community_message_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CM"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CM"."-".$model->attributes['id']."-".uniqid();

            $model->save();

        });

        static::deleting(function ($model){

            $model->chatAssets()->delete();

        });

    }
}
