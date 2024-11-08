<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = ['product_id', 'color', 'size', 'quantity', 'availability'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
