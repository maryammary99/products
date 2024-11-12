<?php
// ImportProducts.php
namespace App\Console\Commands;

use App\Services\ProductImportService;
use Illuminate\Console\Command;
use App\Jobs\ImportProductsJob;

class ImportProducts extends Command
{
    protected $signature = 'import:products';
    protected $description = 'Imports products into the database';

    private $productImportService;

    public function __construct(ProductImportService $productImportService)
    {
        parent::__construct();
        $this->productImportService = $productImportService;
    }

    public function handle()
    {
        $filePath = storage_path('app/products.csv');
        try {
            $this->productImportService->importFromCSV($filePath);
            $this->info("Product import completed.");
        } catch (\Exception $e) {
            $this->error("Failed to import products: " . $e->getMessage());
        }

        ImportProductsJob::dispatch($filePath);


        $this->info("The import job has been dispatched to the queue.");
    }
}
