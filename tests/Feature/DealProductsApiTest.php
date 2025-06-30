<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class DealProductsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_deal_products()
    {
        // Create a category
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'is_active' => true
        ]);

        // Create a deal product
        $dealProduct = Product::factory()->create([
            'name' => 'Deal Product',
            'category_id' => $category->id,
            'price' => 100.00,
            'is_active' => true,
            'is_deal' => true,
            'discount' => 20.00,
            'deal_end_date' => now()->addDays(7)
        ]);

        // Create product image
        ProductImage::factory()->create([
            'product_id' => $dealProduct->id,
            'image_path' => 'test-image.jpg',
            'is_primary' => true
        ]);

        // Create a non-deal product (should not appear in results)
        $regularProduct = Product::factory()->create([
            'name' => 'Regular Product',
            'category_id' => $category->id,
            'price' => 50.00,
            'is_active' => true,
            'is_deal' => false
        ]);

        $response = $this->getJson('/api/products/deals');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'deals' => [
                            '*' => [
                                'id',
                                'name',
                                'description',
                                'image',
                                'category',
                                'price',
                                'discounted_price',
                                'discount_percentage',
                                'unit',
                                'deal_end_date',
                                'deal_end_date_formatted',
                                'time_remaining',
                                'is_available'
                            ]
                        ],
                        'pagination'
                    ]
                ]);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData['deals']);
        $this->assertEquals('Deal Product', $responseData['deals'][0]['name']);
        $this->assertEquals(80.00, $responseData['deals'][0]['discounted_price']);
        $this->assertEquals(20.00, $responseData['deals'][0]['discount_percentage']);
    }

    public function test_deal_products_exclude_expired_deals()
    {
        // Create a category
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'is_active' => true
        ]);

        // Create an expired deal product
        $expiredDeal = Product::factory()->create([
            'name' => 'Expired Deal',
            'category_id' => $category->id,
            'price' => 100.00,
            'is_active' => true,
            'is_deal' => true,
            'discount' => 20.00,
            'deal_end_date' => now()->subDays(1) // Expired yesterday
        ]);

        // Create an active deal product
        $activeDeal = Product::factory()->create([
            'name' => 'Active Deal',
            'category_id' => $category->id,
            'price' => 100.00,
            'is_active' => true,
            'is_deal' => true,
            'discount' => 20.00,
            'deal_end_date' => now()->addDays(7) // Active for 7 more days
        ]);

        $response = $this->getJson('/api/products/deals');

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData['deals']);
        $this->assertEquals('Active Deal', $responseData['deals'][0]['name']);
    }

    public function test_deal_products_can_be_filtered_by_category()
    {
        // Create categories
        $category1 = Category::factory()->create(['name' => 'Category 1', 'is_active' => true]);
        $category2 = Category::factory()->create(['name' => 'Category 2', 'is_active' => true]);

        // Create deal products in different categories
        $deal1 = Product::factory()->create([
            'name' => 'Deal in Category 1',
            'category_id' => $category1->id,
            'is_active' => true,
            'is_deal' => true,
            'deal_end_date' => now()->addDays(7)
        ]);

        $deal2 = Product::factory()->create([
            'name' => 'Deal in Category 2',
            'category_id' => $category2->id,
            'is_active' => true,
            'is_deal' => true,
            'deal_end_date' => now()->addDays(7)
        ]);

        $response = $this->getJson('/api/products/deals?category_id=' . $category1->id);

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData['deals']);
        $this->assertEquals('Deal in Category 1', $responseData['deals'][0]['name']);
    }

    public function test_deal_products_can_be_searched()
    {
        // Create a category
        $category = Category::factory()->create(['name' => 'Test Category', 'is_active' => true]);

        // Create deal products with different names
        $deal1 = Product::factory()->create([
            'name' => 'Apple Deal',
            'category_id' => $category->id,
            'is_active' => true,
            'is_deal' => true,
            'deal_end_date' => now()->addDays(7)
        ]);

        $deal2 = Product::factory()->create([
            'name' => 'Banana Deal',
            'category_id' => $category->id,
            'is_active' => true,
            'is_deal' => true,
            'deal_end_date' => now()->addDays(7)
        ]);

        $response = $this->getJson('/api/products/deals?search=Apple');

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData['deals']);
        $this->assertEquals('Apple Deal', $responseData['deals'][0]['name']);
    }
} 