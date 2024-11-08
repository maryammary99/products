<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductApiService;

class SyncProductsFromAPI extends Command
{
    protected $signature = 'sync:products';
    protected $description = 'Synchronize products from the external API';
    protected $productApiService;

    public function __construct(ProductApiService $productApiService)
    {
        parent::__construct();
        $this->productApiService = $productApiService;
    }

    public function handle()
    {
        $this->productApiService->fetchAndUpdateProducts();
        $this->info('Product synchronization from API completed successfully');
    }
}
