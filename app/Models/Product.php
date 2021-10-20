<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

}
