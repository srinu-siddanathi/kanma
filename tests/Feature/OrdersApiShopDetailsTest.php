<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Branch;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class OrdersApiShopDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_orders_api_includes_shop_details()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'customer']);
        $branch = Branch::factory()->create();
        $shop = Shop::factory()->create([
            'name' => 'Test Shop',
            'description' => 'Test Shop Description',
            'is_active' => true,
            'is_verified' => true
        ]);
        $product = Product::factory()->create();

        // Create an order with shop_id
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'shop_id' => $shop->id,
            'status' => 'pending',
            'total_amount' => 100.00
        ]);

        // Create order item
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00,
            'subtotal' => 100.00
        ]);

        // Authenticate user
        Sanctum::actingAs($user);

        // Make API request
        $response = $this->getJson('/api/orders');

        // Assert response structure
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'id',
                            'status',
                            'total_amount',
                            'payment_method',
                            'payment_status',
                            'delivery_address',
                            'created_at',
                            'created_at_utc',
                            'created_at_asia_kolkata',
                            'updated_at',
                            'items' => [
                                '*' => [
                                    'id',
                                    'product' => [
                                        'id',
                                        'name',
                                        'image_path'
                                    ],
                                    'quantity',
                                    'price',
                                    'subtotal'
                                ]
                            ],
                            'branch' => [
                                'id',
                                'name'
                            ],
                            'shop' => [
                                'id',
                                'name',
                                'description',
                                'image_path',
                                'is_active',
                                'is_verified'
                            ]
                        ]
                    ],
                    'timezone_info'
                ]);

        // Assert shop details are included
        $response->assertJsonFragment([
            'shop' => [
                'id' => $shop->id,
                'name' => 'Test Shop',
                'description' => 'Test Shop Description',
                'image_path' => $shop->image_url,
                'is_active' => true,
                'is_verified' => true
            ]
        ]);
    }

    public function test_user_orders_api_handles_orders_without_shop()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'customer']);
        $branch = Branch::factory()->create();
        $product = Product::factory()->create();

        // Create an order without shop_id
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'shop_id' => null,
            'status' => 'pending',
            'total_amount' => 100.00
        ]);

        // Create order item
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00,
            'subtotal' => 100.00
        ]);

        // Authenticate user
        Sanctum::actingAs($user);

        // Make API request
        $response = $this->getJson('/api/orders');

        // Assert response structure
        $response->assertStatus(200);

        // Assert shop is null for orders without shop
        $response->assertJsonFragment([
            'shop' => null
        ]);
    }

    public function test_order_details_api_includes_shop_details()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'customer']);
        $branch = Branch::factory()->create();
        $shop = Shop::factory()->create([
            'name' => 'Test Shop',
            'description' => 'Test Shop Description',
            'is_active' => true,
            'is_verified' => true
        ]);
        $product = Product::factory()->create();

        // Create an order with shop_id
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'shop_id' => $shop->id,
            'status' => 'pending',
            'total_amount' => 100.00
        ]);

        // Create order item
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00,
            'subtotal' => 100.00
        ]);

        // Authenticate user
        Sanctum::actingAs($user);

        // Make API request
        $response = $this->getJson("/api/orders/{$order->id}");

        // Assert response structure
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'id',
                        'shop'
                    ]
                ]);

        // Assert shop details are included
        $response->assertJsonFragment([
            'shop' => [
                'id' => $shop->id,
                'name' => 'Test Shop',
                'description' => 'Test Shop Description',
                'image_path' => $shop->image_url,
                'is_active' => true,
                'is_verified' => true
            ]
        ]);
    }
} 