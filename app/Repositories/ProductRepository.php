<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function saveOrUpdateProduct($data)
    {
        return Product::updateOrCreate(['id' => $data['id']], $data);
    }

    public function softDeleteOutdatedProducts(array $existingProductIds)
    {
        Product::whereNotIn('id', $existingProductIds)->update(['deleted_at' => now(), 'deletion_reason' => 'Outdated']);
    }
}
