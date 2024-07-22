<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MermaidPayment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user() {
        return $this->belongsTo(User::class)->withDefault();
    }
    public function mermaid() {
        return $this->belongsTo(Mermaid::class, 'mermaid_id', 'id')->withDefault();
    }
   public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['unique_id'] = "MP-".uniqid();
        });
        static::created(function($model) {
            $model->attributes['unique_id'] = "MP-".$model->attributes['id']."-".uniqid();
            
            $model->save();
        });
        static::deleting(function ($model) {
        });
    }
}