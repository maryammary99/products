<?php
// ImportProductsTest.php
namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    public function testProductImport()
    {
        $service = new ProductImportService();
        $filePath = storage_path('app/products.csv');
        $service->importFromCSV($filePath);

        $this->assertDatabaseCount('products', 5); 
    }

    public function testSoftDeleteOutdatedProducts()
    {
        $service = new ProductImportService();
        $filePath = storage_path('app/products.csv');
        $service->importFromCSV($filePath);

        $this->assertDatabaseHas('products', [
            'sku' => 'old-sku',
            'deleted_at' => now(),
        ]);
    }
}
