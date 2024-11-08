<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProductApiService;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductApiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productApiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productApiService = app(ProductApiService::class);
    }

    public function test_fetch_and_update_products_creates_records()
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1,
                    'name' => 'Test Product',
                    'price' => 100,
                    'variations' => [
                        ['color' => 'Red', 'quantity' => 5]
                    ],
                ]
            ], 200)
        ]);

        $this->productApiService->fetchAndUpdateProducts();

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
        $this->assertDatabaseHas('product_variations', ['color' => 'Red']);
    }

    public function test_logs_error_when_api_fails()
    {
        Log::shouldReceive('error')->once();
        
        Http::fake(['*' => Http::response([], 500)]); // Simulate API error
        
        $this->productApiService->fetchAndUpdateProducts();
    }
}
