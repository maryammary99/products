<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class SyncProductsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_products_command()
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1,
                    'name' => 'Synced Product',
                    'price' => 150,
                    'variations' => []
                ]
            ], 200)
        ]);

        Artisan::call('sync:products');

        $this->assertDatabaseHas('products', [
            'name' => 'Synced Product'
        ]);
    }
}
