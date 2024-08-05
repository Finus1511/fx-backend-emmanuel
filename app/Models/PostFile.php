<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

use Log, Validator, Exception, DB, Setting;

class PostFile extends Model
{
    protected $fillable = ['file', 'post_id', 'youtube_link', 'user_id', 'preview_file', 'file_type'];

    protected $hidden = ['deleted_at', 'id', 'unique_id', 'file', 'youtube_link'];

	protected $appends = ['post_file_id', 'post_file_unique_id'];

    public function getPostFileIdAttribute() {

        return $this->id;
    }

    public function getPostFileUniqueIdAttribute() {

        return $this->unique_id;
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return 
            $query->select(
            'post_files.*',
            'post_files.file as post_file',
            'post_files.youtube_link as youtube_url'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlurResponse($query) {

        return $query->select(
            'post_files.*',
            'post_files.blur_file as post_file',
            'post_files.preview_file as youtube_url'
            );
    
    }

    public function posts() {

        return $this->belongsTo(Post::class,'post_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PF"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PF"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {
        
            Helper::storage_delete_file($model->file, POST_TEMP_PATH.$model->user_id.'/');

            Helper::storage_delete_file($model->file, POST_PATH.$model->user_id.'/');

            Helper::storage_delete_file($model->blur_file, POST_BLUR_PATH.$model->user_id.'/');

            Helper::storage_delete_file($model->preview_file, POST_PATH.$model->user_id.'/');

        });

    }
}
