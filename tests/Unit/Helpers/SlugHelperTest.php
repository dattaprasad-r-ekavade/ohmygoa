<?php

namespace Tests\Unit\Helpers;

use App\Helpers\SlugHelper;
use App\Models\BusinessListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugHelperTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_slug_from_title()
    {
        $slug = SlugHelper::generate('My Test Business', BusinessListing::class);

        $this->assertEquals('my-test-business', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_when_duplicate_exists()
    {
        BusinessListing::factory()->create([
            'business_name' => 'Test Business',
            'slug' => 'test-business',
        ]);

        $slug = SlugHelper::generate('Test Business', BusinessListing::class);

        $this->assertEquals('test-business-1', $slug);
    }

    /** @test */
    public function it_generates_incrementing_unique_slugs()
    {
        BusinessListing::factory()->create(['slug' => 'test-business']);
        BusinessListing::factory()->create(['slug' => 'test-business-1']);
        BusinessListing::factory()->create(['slug' => 'test-business-2']);

        $slug = SlugHelper::generate('Test Business', BusinessListing::class);

        $this->assertEquals('test-business-3', $slug);
    }

    /** @test */
    public function it_excludes_current_record_when_checking_uniqueness()
    {
        $listing = BusinessListing::factory()->create([
            'business_name' => 'Test Business',
            'slug' => 'test-business',
        ]);

        // When updating the same record, slug should remain same
        $slug = SlugHelper::generate('Test Business', BusinessListing::class, $listing->id);

        $this->assertEquals('test-business', $slug);
    }

    /** @test */
    public function it_cleans_special_characters_from_slug()
    {
        $cleaned = SlugHelper::clean('test-@#$-business-!@#');

        $this->assertEquals('test-business', $cleaned);
    }

    /** @test */
    public function it_removes_multiple_consecutive_hyphens()
    {
        $cleaned = SlugHelper::clean('test---business--name');

        $this->assertEquals('test-business-name', $cleaned);
    }

    /** @test */
    public function it_trims_hyphens_from_start_and_end()
    {
        $cleaned = SlugHelper::clean('-test-business-');

        $this->assertEquals('test-business', $cleaned);
    }

    /** @test */
    public function it_converts_to_lowercase()
    {
        $cleaned = SlugHelper::clean('Test-Business-NAME');

        $this->assertEquals('test-business-name', $cleaned);
    }

    /** @test */
    public function it_handles_empty_string()
    {
        $cleaned = SlugHelper::clean('');

        $this->assertEquals('', $cleaned);
    }

    /** @test */
    public function it_handles_unicode_characters()
    {
        $slug = SlugHelper::generate('Café Restaurant', BusinessListing::class);

        // Laravel's Str::slug handles unicode properly
        $this->assertNotEmpty($slug);
        $this->assertStringNotContainsString(' ', $slug);
    }

    /** @test */
    public function it_handles_numbers_in_slug()
    {
        $slug = SlugHelper::generate('Business 123', BusinessListing::class);

        $this->assertEquals('business-123', $slug);
    }

    /** @test */
    public function it_handles_very_long_titles()
    {
        $longTitle = str_repeat('Very Long Business Name ', 20);
        
        $slug = SlugHelper::generate($longTitle, BusinessListing::class);

        $this->assertNotEmpty($slug);
        $this->assertIsString($slug);
    }
}
