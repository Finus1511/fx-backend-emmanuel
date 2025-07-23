<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Setting;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attributes', // JSON column like { "Color": "Red", "Size": "M" }
        'price',
        'stock'
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    protected $appends = ['product_variant_id','price_formatted'];

    public function getProductVariantIdAttribute() {

        return $this->id;
    }

    public function getPriceFormattedAttribute() {

        return formatted_amount($this->price);
    }

    public function product()
    {
        return $this->belongsTo(UserProduct::class);
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PV"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PV"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

        });

    }
}
