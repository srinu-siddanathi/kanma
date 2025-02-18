<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Clear existing orders
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get available data
        $users = User::where('role', 'customer')->get();
        $branches = Branch::all();
        $products = Product::all();

        // Check if we have necessary data
        if ($users->isEmpty() || $branches->isEmpty() || $products->isEmpty()) {
            $this->command->info('Skipping OrderSeeder: Missing required data');
            return;
        }

        // Create sample orders
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $branch = $branches->random();
            $orderProducts = $products->random(rand(1, 3));

            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'order_type' => 'regular',
                'delivery_address' => '123 Sample Street, City',
                'delivery_latitude' => 17.4486 + (rand(-100, 100) / 1000),
                'delivery_longitude' => 78.3908 + (rand(-100, 100) / 1000),
                'notes' => 'Sample order notes',
                'total_amount' => 0, // Will be calculated based on items
                'status' => collect(['pending', 'processing', 'completed'])->random(),
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);

            // Add order items
            $total = 0;
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $quantity * $price;
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update order total
            $order->update(['total_amount' => $total]);
        }
    }
} 