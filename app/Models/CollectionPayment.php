<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionPayment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user() {
        return $this->belongsTo(User::class)->withDefault();
    }
    public function collection() {
        return $this->belongsTo(Collection::class, 'collection_id', 'id')->withDefault();
    }
   public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CP-".uniqid();
        });
        static::created(function($model) {
            $model->attributes['unique_id'] = "CP-".$model->attributes['id']."-".uniqid();
            
            $model->save();
        });
        static::deleting(function ($model) {
        });
    }
}