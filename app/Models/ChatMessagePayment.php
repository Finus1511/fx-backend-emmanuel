<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessagePayment extends Model
{
    use HasFactory;
   protected $guarded = ['id'];
    public function fromUser() {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }
    public function toUser() {
        return $this->belongsTo(User::class, 'to_user_id')->withDefault();
    }
   public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CMP-".uniqid();
        });
        static::created(function($model) {
            $model->attributes['unique_id'] = "CMP-".$model->attributes['id']."-".uniqid();
            
            $model->save();
        });
        static::deleting(function ($model) {
        });
    }
}