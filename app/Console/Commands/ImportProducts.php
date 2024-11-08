<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductImportService;

class ImportProducts extends Command
{
    protected $signature = 'import:products';
    protected $description = 'Imports products into the database';
    protected $importerService;

    public function __construct(ProductImportService $importerService)
    {
        parent::__construct();
        $this->importerService = $importerService;
    }

    public function handle()
    {
        $this->importerService->importFromCSV(storage_path('app/products.csv'));
        $this->info('Product import completed successfully');
    }
}
