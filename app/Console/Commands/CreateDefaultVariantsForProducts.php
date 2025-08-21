<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariant;

class CreateDefaultVariantsForProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:create-default-variants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default variants for products that don\'t have any variants yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to create default variants for products...');

        // Get products that don't have any variants
        $productsWithoutVariants = Product::whereDoesntHave('variants')->get();

        if ($productsWithoutVariants->isEmpty()) {
            $this->info('All products already have variants. Nothing to do.');
            return 0;
        }

        $this->info("Found {$productsWithoutVariants->count()} products without variants.");

        $bar = $this->output->createProgressBar($productsWithoutVariants->count());
        $bar->start();

        $createdCount = 0;

        foreach ($productsWithoutVariants as $product) {
            try {
                // Create default variant
                $product->variants()->create([
                    'quantity' => 1,
                    'unit' => 'kg', // Default unit
                    'price' => $product->price ?? 0,
                    'stock' => 100, // Default stock
                    'is_active' => true
                ]);

                $createdCount++;
            } catch (\Exception $e) {
                $this->error("Failed to create variant for product ID {$product->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully created default variants for {$createdCount} products.");
        $this->info('Default variant settings:');
        $this->info('- Quantity: 1');
        $this->info('- Unit: kg');
        $this->info('- Stock: 100');
        $this->info('- Active: true');

        return 0;
    }
} 