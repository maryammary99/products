<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductImportService
{
    // URL de l'API externe
    private const EXTERNAL_API_URL = 'https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products';

    /**
     * Import products from a CSV file.
     *
     * @param string $filePath
     * @return void
     * @throws \Exception
     */
    public function importFromCSV(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File not found');
        }

        $contents = file_get_contents($filePath);
        $lines = explode("\n", $contents);
        $csvSKUs = [];

        DB::beginTransaction();
        try {
            foreach ($lines as $line) {
                $fields = str_getcsv($line, ';');
                $sku = $fields[2] ?? null;
                if (!$sku) continue;
                $csvSKUs[] = $sku;
                
                $price = is_numeric($fields[3]) ? (float)$fields[3] : 0.00;
                
                $product = Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'name' => $fields[1] ?? '',
                        'price' => $price,
                        'currency' => $fields[4] ?? 'USD',
                        'status' => $fields[7] ?? '',
                    ]
                );

                // Decode the JSON string for variations
                $variations = json_decode($fields[5] ?? '[]', true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $variations = []; 
                }

                $this->handleVariations($product, $variations);
            }

            // Soft delete products not in CSV
            Product::whereNotIn('sku', $csvSKUs)->update(['deleted_at' => now()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to import products: " . $e->getMessage());
        }
    }

    /**
     * Synchronize products with the external API.
     *
     * @return void
     * @throws \Exception
     */
    public function syncWithExternalAPI()
    {
        DB::beginTransaction();
        try {
            // Step 1: Fetch products from the external API
            $response = Http::get(self::EXTERNAL_API_URL);

            if ($response->failed()) {
                throw new \Exception("Failed to fetch data from external API.");
            }

            $externalProducts = $response->json();
            $externalSKUs = [];

            // Step 2: Iterate over external products and update/create in the database
            foreach ($externalProducts as $productData) {
                $sku = $productData['sku'] ?? null;
                if (!$sku) continue;
                $externalSKUs[] = $sku;

                // Prepare product data
                $productDataToSave = [
                    'name' => $productData['name'] ?? '',
                    'price' => $productData['price'] ?? 0.00,
                    'currency' => $productData['currency'] ?? 'USD',
                    'status' => $productData['status'] ?? 'active',
                    'sku' => $sku,
                    'image' => $productData['image'] ?? null,
                ];

                // Update or create the product
                $product = Product::updateOrCreate(
                    ['sku' => $sku],
                    $productDataToSave
                );

                // Check if the product was created or updated and log the action
                if ($product->wasRecentlyCreated) {
                    Log::info("Product created", ['sku' => $sku, 'name' => $productData['name']]);
                } else {
                    Log::info("Product updated", ['sku' => $sku, 'name' => $productData['name']]);
                }

                // Import variations
                if (!empty($productData['variations'])) {
                    $this->importVariations($product->id, $productData['variations']);
                }
            }

            // Step 3: Soft delete any products not in the external API response and log them
            $deletedProducts = Product::whereNotIn('sku', $externalSKUs)->get();
            foreach ($deletedProducts as $deletedProduct) {
                $deletedProduct->update(['deleted_at' => now()]);
                Log::info("Product soft deleted", ['sku' => $deletedProduct->sku, 'name' => $deletedProduct->name]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to sync with external API: " . $e->getMessage());
        }
    }

    /**
     * Synchronize product variations.
     *
     * @param Product $product
     * @param array $variations
     * @return void
     */
    private function handleVariations(Product $product, array $variations)
    {
        foreach ($variations as $variationData) {
            ProductVariation::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $variationData['color'] ?? '',  
                    'value' => $variationData['material'] ?? '' 
                ],
                [
                    'quantity' => $variationData['quantity'] ?? 0,
                    'availability' => $variationData['availability'] ?? true
                ]
            );
        }
    }
}
