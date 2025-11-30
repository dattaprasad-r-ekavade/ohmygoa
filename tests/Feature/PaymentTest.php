<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_initiate_subscription_payment()
    {
        $user = User::factory()->create(['role' => 'business']);

        $response = $this->actingAs($user)->post('/payments/subscription', [
            'plan' => 'premium',
            'duration' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'order_id',
            'payment_id',
            'amount',
            'currency',
        ]);
    }

    /** @test */
    public function free_users_cannot_initiate_subscription_payment()
    {
        $user = User::factory()->create(['role' => 'free']);

        $response = $this->actingAs($user)->post('/payments/subscription', [
            'plan' => 'premium',
            'duration' => 1,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function payment_creates_database_record()
    {
        $user = User::factory()->create(['role' => 'business']);

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment($user, 499, 'Subscription Payment');

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'payment_id' => $result['payment_id'],
            'amount' => 499,
            'currency' => 'INR',
            'payment_status' => 'pending',
        ]);
    }

    /** @test */
    public function payment_verification_updates_status()
    {
        $user = User::factory()->create();

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment($user, 500, 'Test Payment');

        $verified = $paymentService->verifyPayment(
            $result['payment_id'],
            $result['order_id'],
            'mock_signature'
        );

        $this->assertTrue($verified);
        $this->assertDatabaseHas('payments', [
            'payment_id' => $result['payment_id'],
            'payment_status' => 'completed',
        ]);
    }

    /** @test */
    public function failed_payment_updates_status()
    {
        $user = User::factory()->create();

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment($user, 500, 'Test Payment');

        $paymentService->handlePaymentFailure($result['payment_id'], 'Insufficient funds');

        $this->assertDatabaseHas('payments', [
            'payment_id' => $result['payment_id'],
            'payment_status' => 'failed',
            'failure_reason' => 'Insufficient funds',
        ]);
    }

    /** @test */
    public function users_can_purchase_coupons()
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create([
            'price' => 299,
            'is_active' => true,
            'available_quantity' => 10,
        ]);

        $response = $this->actingAs($user)->post("/coupons/{$coupon->id}/purchase");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'order_id',
            'payment_id',
            'amount',
        ]);
    }

    /** @test */
    public function coupon_purchase_deducts_commission()
    {
        $businessOwner = User::factory()->create(['role' => 'business']);
        $customer = User::factory()->create();
        
        $coupon = Coupon::factory()->create([
            'user_id' => $businessOwner->id,
            'price' => 100,
            'is_active' => true,
        ]);

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment($customer, $coupon->price, 'Coupon Purchase', [
            'coupon_id' => $coupon->id,
            'business_id' => $businessOwner->id,
        ]);

        // Verify payment
        $paymentService->verifyPayment($result['payment_id'], $result['order_id']);

        $payment = Payment::where('payment_id', $result['payment_id'])->first();

        // Commission should be deducted (10% of 100 = 10)
        $this->assertEquals(100, $payment->amount);
        $this->assertEquals(10, $payment->commission_amount);
        $this->assertEquals(90, $payment->net_amount);
    }

    /** @test */
    public function completed_payment_sends_receipt_email()
    {
        $user = User::factory()->create();

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment($user, 499, 'Subscription');

        $paymentService->verifyPayment($result['payment_id'], $result['order_id']);

        // Email should be queued
        $this->assertDatabaseHas('jobs', []);
    }

    /** @test */
    public function users_can_view_payment_history()
    {
        $user = User::factory()->create();

        Payment::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard/payments');

        $response->assertStatus(200);
        $response->assertViewHas('payments');
    }

    /** @test */
    public function payment_amount_is_converted_to_paise()
    {
        $user = User::factory()->create();

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment($user, 499, 'Test');

        // Razorpay expects amount in paise (smallest unit)
        $this->assertEquals(49900, $result['amount']); // 499 * 100
    }

    /** @test */
    public function refund_can_be_processed()
    {
        $user = User::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'amount' => 500,
            'payment_status' => 'completed',
        ]);

        $paymentService = app(PaymentService::class);
        $refund = $paymentService->createRefund($payment, 500);

        $this->assertArrayHasKey('refund_id', $refund);
        $this->assertEquals(500, $refund['amount']);
        
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'refund_status' => 'refunded',
            'refund_amount' => 500,
        ]);
    }
}
