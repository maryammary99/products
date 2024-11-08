<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'currency', 'status', 'variations'];
    protected $dates = ['deleted_at'];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
}
