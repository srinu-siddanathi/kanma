<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class WalletPaymentOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $branch;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create([
            'wallet_balance' => 1000, // Give user sufficient wallet balance
            'role' => 'customer'
        ]);
        
        $this->branch = Branch::factory()->create([
            'is_active' => true
        ]);
        
        $this->product = Product::factory()->create([
            'price' => 100,
            'is_active' => true
        ]);

        // Set up required settings
        Setting::set('minimum_order_amount', 50);
        Setting::set('small_cart_fee', 10);
    }

    public function test_order_automatically_confirmed_when_fully_paid_by_wallet()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'branch_id' => $this->branch->id,
                'delivery_address' => '123 Test Street, Test City',
                'delivery_latitude' => 12.9716,
                'delivery_longitude' => 77.5946,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 1
                    ]
                ],
                'payment_method' => 'wallet',
                'wallet_amount_used' => 150, // Product price (100) + delivery fee (50) + small cart fee (0)
                'notes' => 'Test wallet payment order'
            ]);

        if ($response->status() !== 200) {
            $this->fail('Request failed with status ' . $response->status() . ': ' . $response->getContent());
        }
        
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order created successfully'
            ]);

        // Get the created order
        $order = Order::where('user_id', $this->user->id)->latest()->first();
        
        // Verify order is automatically confirmed
        $this->assertEquals('confirmed', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(150, $order->wallet_amount_used);
        $this->assertEquals(0, $order->total_amount); // Should be 0 since fully paid by wallet
    }

    public function test_order_remains_pending_when_partially_paid_by_wallet()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'branch_id' => $this->branch->id,
                'delivery_address' => '123 Test Street, Test City',
                'delivery_latitude' => 12.9716,
                'delivery_longitude' => 77.5946,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 1
                    ]
                ],
                'payment_method' => 'wallet',
                'wallet_amount_used' => 75, // Only partial payment
                'notes' => 'Test partial wallet payment order'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order created successfully'
            ]);

        // Get the created order
        $order = Order::where('user_id', $this->user->id)->latest()->first();
        
        // Verify order remains pending
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals(75, $order->wallet_amount_used);
        $this->assertEquals(75, $order->total_amount); // Remaining amount to be paid
    }

    public function test_order_remains_pending_when_no_wallet_payment()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'branch_id' => $this->branch->id,
                'delivery_address' => '123 Test Street, Test City',
                'delivery_latitude' => 12.9716,
                'delivery_longitude' => 77.5946,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 1
                    ]
                ],
                'payment_method' => 'cod',
                'wallet_amount_used' => 0,
                'notes' => 'Test COD order'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order created successfully'
            ]);

        // Get the created order
        $order = Order::where('user_id', $this->user->id)->latest()->first();
        
        // Verify order remains pending
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals(0, $order->wallet_amount_used);
        $this->assertEquals(150, $order->total_amount); // Full amount to be paid
    }

    public function test_wallet_balance_deducted_correctly()
    {
        $initialBalance = $this->user->wallet_balance;
        $walletAmountUsed = 150;

        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'branch_id' => $this->branch->id,
                'delivery_address' => '123 Test Street, Test City',
                'delivery_latitude' => 12.9716,
                'delivery_longitude' => 77.5946,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 1
                    ]
                ],
                'payment_method' => 'wallet',
                'wallet_amount_used' => $walletAmountUsed,
                'notes' => 'Test wallet deduction'
            ]);

        $response->assertStatus(200);

        // Refresh user to get updated wallet balance
        $this->user->refresh();
        
        // Verify wallet balance is deducted
        $this->assertEquals($initialBalance - $walletAmountUsed, $this->user->wallet_balance);
    }

    public function test_insufficient_wallet_balance_rejected()
    {
        // Set user wallet balance to less than required amount
        $this->user->update(['wallet_balance' => 50]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'branch_id' => $this->branch->id,
                'delivery_address' => '123 Test Street, Test City',
                'delivery_latitude' => 12.9716,
                'delivery_longitude' => 77.5946,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 1
                    ]
                ],
                'payment_method' => 'wallet',
                'wallet_amount_used' => 150, // More than available balance
                'notes' => 'Test insufficient balance'
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Insufficient wallet balance. Available: 50, Required: 150'
            ]);
    }

    public function test_wallet_amount_exceeding_order_total_rejected()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'branch_id' => $this->branch->id,
                'delivery_address' => '123 Test Street, Test City',
                'delivery_latitude' => 12.9716,
                'delivery_longitude' => 77.5946,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 1
                    ]
                ],
                'payment_method' => 'wallet',
                'wallet_amount_used' => 200, // More than order total (160)
                'notes' => 'Test excessive wallet amount'
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Wallet amount cannot exceed order total. Order total: 160, Wallet amount: 200'
            ]);
    }
} 