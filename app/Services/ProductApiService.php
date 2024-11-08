<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\ProductVariation;

class ProductApiService
{
    protected $apiUrl = 'https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products';

    public function fetchAndUpdateProducts()
    {
        $response = Http::get($this->apiUrl);
        
        if ($response->successful()) {
            foreach ($response->json() as $product) {
                if (isset($product['name'], $product['price'], $product['id'])) {
                    $productData = [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'sku' => $product['id'],
                        'image' => $product['image'] ?? null,
                    ];

                    $this->updateOrCreateProduct($productData);

                    if (!empty($product['variations'])) {
                        $this->importVariations($product['id'], $product['variations']);
                    }
                } else {
                    \Log::warning('Missing necessary product data', $product);
                }
            }
        } else {
            \Log::error('Failed to fetch products from API');
        }
    }

    public function updateOrCreateProduct($productData)
    {
        Product::updateOrCreate(['sku' => $productData['sku']], $productData);
    }

    public function importVariations($productId, $variations)
    {
        $product = Product::find($productId);

        if (!$product) {
            \Log::error('Product not found for ID: ' . $productId);
            return;
        }

        foreach ($variations as $variation) {
            ProductVariation::create([
                'product_id' => $productId,
                'color' => $variation['color'] ?? null,
                'material' => $variation['material'] ?? null,
                'quantity' => $variation['quantity'] ?? 0,
                'additional_price' => $variation['additional_price'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
