<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductVariation;
use App\Models\Product;

class ProductImportService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function importFromCSV($filePath)
    {
        $file = fopen($filePath, 'r');
        $row = 0;
        
        while (($data = fgetcsv($file, 1000, ";")) !== false) {
            if ($row++ === 0) continue;
            
            $productData = [
                'id' => $data[0],
                'name' => $data[1],
                'sku' => $data[2],
                'price' => $data[3],
                'currency' => $data[4],
                'status' => $data[5],
                'variations' => json_encode($data[6], true), 
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $product = $this->validateAndSave($productData);
            
            if ($product === null) {
                \Log::error('Product creation failed', ['product_data' => $productData]);
                continue;
            }

            $importedProductIds[] = $productData['id'];
            
            if (!empty($productData['variations'])) {
                $this->importVariations($product->id, $productData['variations']);
            }
        }

        fclose($file);
        $this->softDeleteOldProducts($importedProductIds);
    }

    protected function validateAndSave($productData)
    {
        $validator = Validator::make($productData, [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return null;
        }
        
        return $this->productRepository->saveOrUpdateProduct($productData);
    }
    
    public function softDeleteOldProducts($importedProductIds)
    {
        foreach (array_chunk($importedProductIds, 1000) as $chunk) {
            Product::whereNotIn('id', $chunk)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }
    }

    public function importVariations($productId, $variations)
    {
        $decodedVariations = json_decode($variations, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('JSON decoding failed for variations', ['variations' => $variations]);
            return;
        }

        foreach ($decodedVariations as $variation) {
            ProductVariation::create([
                'product_id' => $productId,
                'size' => $variation['name'] === 'الحجم' ? $variation['value'] : null,
                'color' => $variation['name'] === 'اللون' ? $variation['value'] : null,
                'quantity' => $variation['quantity'] ?? 0,
                'availability' => $variation['availability'] ?? 'In Stock',
            ]);
        }
    }
}
