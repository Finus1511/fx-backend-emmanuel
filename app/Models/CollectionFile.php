<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class CollectionFile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user() {
       return $this->belongsTo(User::class, 'user_id');
    }
    public function collection() {
        return $this->belongsTo(Collection::class)->withDefault();
    }
    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CF-".uniqid();
        });
        static::created(function($model) {
            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);
            $model->attributes['unique_id'] = "CF-".$id;
            
            $model->save();
        });
        static::deleting(function ($model) {
            Helper::storage_delete_file($model->file, COLLECTION_FILE_PATH);
        });
    }
}