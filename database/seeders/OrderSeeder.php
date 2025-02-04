<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role', 'customer')->get();
        $branches = Branch::with('products')->get();
        
        // Create some orders for each customer
        foreach ($customers as $customer) {
            foreach (range(1, 3) as $i) {
                $branch = $branches->random();
                $products = $branch->products->random(rand(1, 3));
                
                $order = Order::create([
                    'user_id' => $customer->id,
                    'branch_id' => $branch->id,
                    'status' => 'pending',
                    'delivery_address' => 'Sample Address, Hyderabad - 500081',
                    'delivery_latitude' => 17.5286,
                    'delivery_longitude' => 78.4308,
                    'notes' => 'Sample order notes',
                    'total_amount' => 0,
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);

                $total = 0;
                foreach ($products as $product) {
                    $quantity = rand(1, 3);
                    $subtotal = $product->price * $quantity;
                    $total += $subtotal;
                    
                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $order->update(['total_amount' => $total]);
            }
        }
    }
} 