<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class MermaidFile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user() {
       return $this->belongsTo(User::class, 'sender_id');
    }
    public function mermaid() {
        return $this->belongsTo(Mermaid::class)->withDefault();
    }
    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "MF-".uniqid();
        });
        static::created(function($model) {
            $id = str_pad($model->attributes['id'], 5, '0', STR_PAD_LEFT);
            $model->attributes['unique_id'] = "MF-".$id;
            
            $model->save();
        });
        static::deleting(function ($model) {
            Helper::storage_delete_file($model->file, MERMAID_FILE_PATH);
        });
    }
}