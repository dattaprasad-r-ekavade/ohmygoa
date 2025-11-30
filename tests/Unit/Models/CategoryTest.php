<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\BusinessListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_business_listings()
    {
        $category = Category::factory()->create();
        
        BusinessListing::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->businessListings);
        $this->assertInstanceOf(BusinessListing::class, $category->businessListings->first());
    }

    /** @test */
    public function it_has_a_parent_category()
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->assertInstanceOf(Category::class, $child->parent);
        $this->assertEquals($parent->id, $child->parent->id);
    }

    /** @test */
    public function it_has_many_subcategories()
    {
        $parent = Category::factory()->create();
        
        Category::factory()->count(3)->create(['parent_id' => $parent->id]);

        $this->assertCount(3, $parent->subcategories);
        $this->assertInstanceOf(Category::class, $parent->subcategories->first());
    }

    /** @test */
    public function it_generates_unique_slug()
    {
        $category1 = Category::factory()->create(['name' => 'Test Category']);
        $category2 = Category::factory()->create(['name' => 'Test Category']);

        $this->assertNotEquals($category1->slug, $category2->slug);
        $this->assertStringContainsString('test-category', $category1->slug);
    }

    /** @test */
    public function it_can_be_active_or_inactive()
    {
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $this->assertTrue($activeCategory->is_active);
        $this->assertFalse($inactiveCategory->is_active);
    }

    /** @test */
    public function it_can_be_popular()
    {
        $popularCategory = Category::factory()->create(['is_popular' => true]);
        $regularCategory = Category::factory()->create(['is_popular' => false]);

        $this->assertTrue($popularCategory->is_popular);
        $this->assertFalse($regularCategory->is_popular);
    }

    /** @test */
    public function it_has_display_order()
    {
        $category1 = Category::factory()->create(['display_order' => 1]);
        $category2 = Category::factory()->create(['display_order' => 2]);

        $this->assertEquals(1, $category1->display_order);
        $this->assertEquals(2, $category2->display_order);
    }

    /** @test */
    public function it_can_have_icon()
    {
        $category = Category::factory()->create(['icon' => 'fas fa-hotel']);

        $this->assertEquals('fas fa-hotel', $category->icon);
    }

    /** @test */
    public function it_can_have_description()
    {
        $description = 'Hotels and resorts in Goa';
        $category = Category::factory()->create(['description' => $description]);

        $this->assertEquals($description, $category->description);
    }

    /** @test */
    public function it_counts_listings_correctly()
    {
        $category = Category::factory()->create();
        
        BusinessListing::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 'approved',
        ]);

        BusinessListing::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => 'pending',
        ]);

        $this->assertCount(7, $category->businessListings);
        $this->assertEquals(5, $category->businessListings()->where('status', 'approved')->count());
    }
}
