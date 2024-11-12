<?php

// Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'sku', 'price', 'status', 'currency'];
    protected $casts = ['variations' => 'array'];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function markAsDeleted($reason)
    {
        $this->update(['deleted_at' => now(), 'deletion_reason' => $reason]);
    }
}
