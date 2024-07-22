<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class Mermaid extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user() {
       return $this->belongsTo(User::class, 'sender_id');
    }
    public function mermaidFiles()
    {
        return $this->hasMany(MermaidFile::class, 'mermaid_id');
    }
    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "M-".uniqid();
        });
        static::created(function($model) {
            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);
            $model->attributes['unique_id'] = "M-".$id;
            
            $model->save();
        });
        static::deleting(function ($model) {
            Helper::storage_delete_file($model->thumbnail, MERMAID_FOLDER_PATH);
            foreach ($model->mermaidFiles as $key => $mermaidFile) {
                $mermaidFile->delete();
            }
        });
    }
}