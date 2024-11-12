<?php

namespace App\Console\Commands;

use App\Services\ProductImportService;
use Illuminate\Console\Command;

class SyncProductsFromAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize products with the external API daily';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Starting product synchronization with the external API...');

        $productImportService = new ProductImportService();

        try {
            // Appel de la méthode de synchronisation avec l'API
            $productImportService->syncWithExternalAPI();
            $this->info('Product synchronization completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error during product synchronization: ' . $e->getMessage());
        }
    }
}
