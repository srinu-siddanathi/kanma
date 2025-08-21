<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\ChatOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ChatOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_chat_order_with_branch_id()
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/chat-orders', [
            'name' => 'Test Order',
            'branch_id' => $branch->id,
            'notes' => 'Test notes'
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Chat order created successfully'
                ]);

        $this->assertDatabaseHas('chat_orders', [
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'name' => 'Test Order',
            'notes' => 'Test notes',
            'status' => 'pending'
        ]);
    }

    public function test_cannot_create_chat_order_without_branch_id()
    {
        $user = User::factory()->create();
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/chat-orders', [
            'name' => 'Test Order',
            'notes' => 'Test notes'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['branch_id']);
    }

    public function test_cannot_create_chat_order_with_invalid_branch_id()
    {
        $user = User::factory()->create();
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/chat-orders', [
            'name' => 'Test Order',
            'branch_id' => 999, // Non-existent branch
            'notes' => 'Test notes'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['branch_id']);
    }

    public function test_can_retrieve_chat_orders_with_branch_data()
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        
        $chatOrder = ChatOrder::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'name' => 'Test Order'
        ]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/chat-orders');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ]);

        $this->assertArrayHasKey('branch', $response->json('data.data.0'));
    }
} 