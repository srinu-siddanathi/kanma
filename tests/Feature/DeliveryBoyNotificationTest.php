<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Shop;
use App\Helpers\NotificationHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;

class DeliveryBoyNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_delivery_boy_receives_notification_when_order_assigned()
    {
        // Mock the NotificationHelper to capture calls
        $mock = Mockery::mock('alias:' . NotificationHelper::class);
        $mock->shouldReceive('sendDeliveryUpdate')->once();
        $mock->shouldReceive('sendOrderAssignmentToDeliveryBoy')->once();

        // Create a branch
        $branch = Branch::factory()->create();

        // Create a branch manager
        $branchManager = User::factory()->create([
            'role' => 'branch_manager',
            'branch_id' => $branch->id
        ]);

        // Create a delivery boy
        $deliveryBoy = User::factory()->create([
            'role' => 'delivery_boy',
            'branch_id' => $branch->id
        ]);

        // Create a customer
        $customer = User::factory()->create([
            'role' => 'customer'
        ]);

        // Create a shop
        $shop = Shop::factory()->create();

        // Create an order
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
            'shop_id' => $shop->id,
            'status' => 'pending',
            'delivery_boy_id' => null,
            'delivery_address' => '123 Test Street',
            'total_amount' => 100.00
        ]);

        // Act as branch manager and assign order to delivery boy
        $response = $this->actingAs($branchManager)
            ->post(route('branch.order-assignments.assign', $order), [
                'delivery_boy_id' => $deliveryBoy->id
            ]);

        // Assert the response
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Order assigned successfully');

        // Assert the order was updated
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'delivery_boy_id' => $deliveryBoy->id,
            'status' => 'assigned'
        ]);

        // Verify that notifications were sent
        $mock->shouldHaveReceived('sendDeliveryUpdate')
            ->with($customer->id, $order->id, 'assigned', $deliveryBoy->name);

        $mock->shouldHaveReceived('sendOrderAssignmentToDeliveryBoy')
            ->with($deliveryBoy->id, $order->id, $customer->name, $order->delivery_address, $order->total_amount);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
