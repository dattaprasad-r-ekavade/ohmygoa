<?php

namespace Tests\Unit\Services;

use App\Models\Payment;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CommissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CommissionService();
    }

    /** @test */
    public function it_has_correct_commission_rate()
    {
        $this->assertEquals(0.10, CommissionService::COMMISSION_RATE);
    }

    /** @test */
    public function it_calculates_commission_correctly()
    {
        $commission = $this->service->calculateCommission(1000);
        $this->assertEquals(100.00, $commission);

        $commission = $this->service->calculateCommission(499);
        $this->assertEquals(49.90, $commission);

        $commission = $this->service->calculateCommission(1500.50);
        $this->assertEquals(150.05, $commission);
    }

    /** @test */
    public function it_calculates_net_amount_correctly()
    {
        $netAmount = $this->service->calculateNetAmount(1000);
        $this->assertEquals(900.00, $netAmount);

        $netAmount = $this->service->calculateNetAmount(499);
        $this->assertEquals(449.10, $netAmount);

        $netAmount = $this->service->calculateNetAmount(1500.50);
        $this->assertEquals(1350.45, $netAmount);
    }

    /** @test */
    public function it_rounds_amounts_to_two_decimals()
    {
        $commission = $this->service->calculateCommission(123.456);
        $this->assertEquals(12.35, $commission); // 12.3456 rounded

        $netAmount = $this->service->calculateNetAmount(123.456);
        $this->assertEquals(111.11, $netAmount); // 123.456 - 12.3456 = 111.1104
    }

    /** @test */
    public function it_processes_commission_on_completed_payment()
    {
        $business = User::factory()->create([
            'role' => 'business',
            'wallet_balance' => 0,
        ]);

        $payment = Payment::factory()->create([
            'user_id' => $business->id,
            'amount' => 1000,
            'payment_status' => 'completed',
            'commission_amount' => null,
            'net_amount' => null,
        ]);

        $this->service->processCommission($payment, $business);

        $this->assertEquals(100.00, $payment->fresh()->commission_amount);
        $this->assertEquals(900.00, $payment->fresh()->net_amount);
        $this->assertEquals(900.00, $business->fresh()->wallet_balance);
    }

    /** @test */
    public function it_does_not_process_commission_on_pending_payment()
    {
        $business = User::factory()->create([
            'role' => 'business',
            'wallet_balance' => 0,
        ]);

        $payment = Payment::factory()->create([
            'user_id' => $business->id,
            'amount' => 1000,
            'payment_status' => 'pending',
        ]);

        $this->service->processCommission($payment, $business);

        $this->assertNull($payment->fresh()->commission_amount);
        $this->assertEquals(0, $business->fresh()->wallet_balance);
    }

    /** @test */
    public function it_does_not_process_commission_on_failed_payment()
    {
        $business = User::factory()->create([
            'role' => 'business',
            'wallet_balance' => 0,
        ]);

        $payment = Payment::factory()->create([
            'user_id' => $business->id,
            'amount' => 1000,
            'payment_status' => 'failed',
        ]);

        $this->service->processCommission($payment, $business);

        $this->assertNull($payment->fresh()->commission_amount);
        $this->assertEquals(0, $business->fresh()->wallet_balance);
    }

    /** @test */
    public function it_gets_commission_statistics()
    {
        $business = User::factory()->create(['role' => 'business']);

        Payment::factory()->create([
            'user_id' => $business->id,
            'amount' => 1000,
            'commission_amount' => 100,
            'net_amount' => 900,
            'payment_status' => 'completed',
        ]);

        Payment::factory()->create([
            'user_id' => $business->id,
            'amount' => 500,
            'commission_amount' => 50,
            'net_amount' => 450,
            'payment_status' => 'completed',
        ]);

        Payment::factory()->create([
            'user_id' => $business->id,
            'amount' => 300,
            'payment_status' => 'pending', // Should not be included
        ]);

        $stats = $this->service->getCommissionStats($business);

        $this->assertEquals(1500, $stats['total_sales']);
        $this->assertEquals(150, $stats['total_commission']);
        $this->assertEquals(1350, $stats['total_earned']);
        $this->assertEquals('10%', $stats['commission_rate']);
    }

    /** @test */
    public function it_returns_zero_stats_for_business_with_no_sales()
    {
        $business = User::factory()->create(['role' => 'business']);

        $stats = $this->service->getCommissionStats($business);

        $this->assertEquals(0, $stats['total_sales']);
        $this->assertEquals(0, $stats['total_commission']);
        $this->assertEquals(0, $stats['total_earned']);
        $this->assertEquals('10%', $stats['commission_rate']);
    }

    /** @test */
    public function it_handles_decimal_commission_calculations()
    {
        // Test with 499 (subscription amount)
        $commission = $this->service->calculateCommission(499);
        $netAmount = $this->service->calculateNetAmount(499);

        $this->assertEquals(49.90, $commission);
        $this->assertEquals(449.10, $netAmount);
        $this->assertEquals(499, $commission + $netAmount);
    }
}
