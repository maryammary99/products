<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Log;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    protected $productImportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productImportService = app(ProductImportService::class);
    }

    public function test_csv_import_creates_products_and_variations()
    {
        $filePath = storage_path('app/test_products.csv'); // create this file with sample data
        $this->productImportService->importFromCSV($filePath);

        $this->assertDatabaseCount('products', 1); // Adjust based on sample data
        $this->assertDatabaseCount('product_variations', 2); // Adjust based on variations in CSV
    }

    public function test_import_skips_invalid_data()
    {
        Log::shouldReceive('error')->once();
        
        // CSV file with missing fields
        $filePath = storage_path('app/invalid_products.csv');
        $this->productImportService->importFromCSV($filePath);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_soft_delete_old_products()
    {
        // Import initial data
        $filePath = storage_path('app/test_products.csv');
        $this->productImportService->importFromCSV($filePath);

        // Soft delete old products
        $this->productImportService->softDeleteOldProducts([1]); // assuming 1 is the ID from CSV

        $this->assertSoftDeleted('products', ['id' => 2]); // assuming 2 is an outdated ID
    }
}
