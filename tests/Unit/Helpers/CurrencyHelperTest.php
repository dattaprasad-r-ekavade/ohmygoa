<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CurrencyHelper;
use Tests\TestCase;

class CurrencyHelperTest extends TestCase
{
    /** @test */
    public function it_has_correct_default_currency()
    {
        $this->assertEquals('₹', CurrencyHelper::DEFAULT_SYMBOL);
        $this->assertEquals('INR', CurrencyHelper::DEFAULT_CODE);
    }

    /** @test */
    public function it_formats_amount_with_two_decimals()
    {
        $formatted = CurrencyHelper::format(1000);

        $this->assertEquals('₹1,000.00', $formatted);
    }

    /** @test */
    public function it_formats_decimal_amounts()
    {
        $formatted = CurrencyHelper::format(1234.56);

        $this->assertEquals('₹1,234.56', $formatted);
    }

    /** @test */
    public function it_formats_with_custom_symbol()
    {
        $formatted = CurrencyHelper::format(1000, '$');

        $this->assertEquals('$1,000.00', $formatted);
    }

    /** @test */
    public function it_formats_whole_numbers_without_decimals()
    {
        $formatted = CurrencyHelper::formatWhole(1000);

        $this->assertEquals('₹1,000', $formatted);
    }

    /** @test */
    public function it_formats_whole_with_custom_symbol()
    {
        $formatted = CurrencyHelper::formatWhole(1234.56, '$');

        $this->assertEquals('$1,235', $formatted); // Rounds to whole number
    }

    /** @test */
    public function it_formats_large_amounts_in_crores()
    {
        $formatted = CurrencyHelper::formatShort(15000000); // 1.5 Cr

        $this->assertEquals('₹1.50 Cr', $formatted);
    }

    /** @test */
    public function it_formats_amounts_in_lakhs()
    {
        $formatted = CurrencyHelper::formatShort(500000); // 5 L

        $this->assertEquals('₹5.00 L', $formatted);
    }

    /** @test */
    public function it_formats_amounts_in_thousands()
    {
        $formatted = CurrencyHelper::formatShort(5000); // 5 K

        $this->assertEquals('₹5.00 K', $formatted);
    }

    /** @test */
    public function it_formats_small_amounts_normally()
    {
        $formatted = CurrencyHelper::formatShort(500);

        $this->assertEquals('₹500.00', $formatted);
    }

    /** @test */
    public function it_handles_zero_amount()
    {
        $formatted = CurrencyHelper::format(0);

        $this->assertEquals('₹0.00', $formatted);
    }

    /** @test */
    public function it_handles_negative_amounts()
    {
        $formatted = CurrencyHelper::format(-1000);

        $this->assertEquals('₹-1,000.00', $formatted);
    }

    /** @test */
    public function it_formats_subscription_amount()
    {
        // Test with actual subscription price
        $formatted = CurrencyHelper::format(499);

        $this->assertEquals('₹499.00', $formatted);
    }

    /** @test */
    public function it_formats_large_indian_numbers()
    {
        // 1 Crore
        $formatted1 = CurrencyHelper::formatShort(10000000);
        $this->assertEquals('₹1.00 Cr', $formatted1);

        // 10 Lakhs
        $formatted2 = CurrencyHelper::formatShort(1000000);
        $this->assertEquals('₹10.00 L', $formatted2);

        // 50 Thousand
        $formatted3 = CurrencyHelper::formatShort(50000);
        $this->assertEquals('₹50.00 K', $formatted3);
    }

    /** @test */
    public function it_rounds_short_format_correctly()
    {
        $formatted = CurrencyHelper::formatShort(1234567); // 1.23 Cr

        $this->assertEquals('₹1.23 Cr', $formatted);
    }

    /** @test */
    public function it_formats_very_large_amounts()
    {
        $formatted = CurrencyHelper::formatShort(100000000); // 10 Cr

        $this->assertEquals('₹10.00 Cr', $formatted);
    }

    /** @test */
    public function it_uses_indian_numbering_system_thresholds()
    {
        // Indian system: K (1000), L (100000), Cr (10000000)
        $this->assertEquals('₹999.00', CurrencyHelper::formatShort(999));
        $this->assertEquals('₹1.00 K', CurrencyHelper::formatShort(1000));
        $this->assertEquals('₹99.99 K', CurrencyHelper::formatShort(99990));
        $this->assertEquals('₹1.00 L', CurrencyHelper::formatShort(100000));
        $this->assertEquals('₹99.99 L', CurrencyHelper::formatShort(9999000));
        $this->assertEquals('₹1.00 Cr', CurrencyHelper::formatShort(10000000));
    }
}
