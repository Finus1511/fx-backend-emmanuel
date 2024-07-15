<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileArchive extends Model
{
    use HasFactory;

    protected $hidden = ['deleted_at', 'id', 'unique_id'];

    protected $appends = ['file_archive_id','file_archive_unique_id'];

    protected $fillable = ['status'];

    public function getFileArchiveIdAttribute() {

        return $this->id;
    }

    public function getFileArchiveUniqueIdAttribute() {

        return $this->unique_id;
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
            'file_archives.id',
            'file_archives.unique_id',
            'file_archives.file',
            'file_archives.file_type',
            'file_archives.origin',
            'file_archives.created_at',
            'file_archives.updated_at'
            );
    
    }

    public static function boot() {

        parent::boot();

        static::created(function($model) {

            $model->attributes['unique_id'] = "ARCH"."-".$model->attributes['id'];

            $model->save();
        
        });

    }
}
