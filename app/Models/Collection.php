<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class Collection extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user() {
       return $this->belongsTo(User::class, 'user_id');
    }

    public function collectionFiles()
    {
        return $this->hasMany(CollectionFile::class, 'collection_id');
    }

    public function collectionPayments() {

        return $this->hasMany(CollectionPayment::class,'collection_id');
    }

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "C-".uniqid();
        });
        static::created(function($model) {
            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);
            $model->attributes['unique_id'] = "C-".$id;
            
            $model->save();
        });
        static::deleting(function ($model) {
            Helper::storage_delete_file($model->thumbnail, COLLECTION_FOLDER_PATH);
            foreach ($model->collectionFiles as $key => $collectionFile) {
                $collectionFile->delete();
            }
        });
    }
}