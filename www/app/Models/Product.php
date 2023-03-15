<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    public function categoryProduct(): HasOne
    {
        return $this->hasOne(CategoryProduct::class, 'product_id');
    }

    public function productImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id');
    }
}
