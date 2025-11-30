<?php

namespace Tests\Unit\Services;

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    /** @test */
    public function it_initializes_payment_with_correct_data()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '9876543210',
        ]);

        $result = $this->service->initializePayment($user, 499, 'Subscription Payment');

        $this->assertArrayHasKey('order_id', $result);
        $this->assertArrayHasKey('payment_id', $result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertArrayHasKey('key', $result);
        $this->assertArrayHasKey('prefill', $result);

        $this->assertEquals(49900, $result['amount']); // 499 * 100 paise
        $this->assertEquals('INR', $result['currency']);
        $this->assertEquals('John Doe', $result['prefill']['name']);
        $this->assertEquals('john@example.com', $result['prefill']['email']);
        $this->assertEquals('9876543210', $result['prefill']['contact']);

        $this->assertStringStartsWith('order_', $result['order_id']);
        $this->assertStringStartsWith('pay_', $result['payment_id']);
    }

    /** @test */
    public function it_creates_payment_record_in_database()
    {
        $user = User::factory()->create();

        $result = $this->service->initializePayment($user, 1000, 'Coupon Purchase', ['coupon_id' => 1]);

        $payment = Payment::where('payment_id', $result['payment_id'])->first();

        $this->assertNotNull($payment);
        $this->assertEquals($user->id, $payment->user_id);
        $this->assertEquals(1000, $payment->amount);
        $this->assertEquals('INR', $payment->currency);
        $this->assertEquals('razorpay', $payment->payment_method);
        $this->assertEquals('pending', $payment->payment_status);
        $this->assertEquals('Coupon Purchase', $payment->purpose);
        $this->assertEquals(['coupon_id' => 1], $payment->metadata);
    }

    /** @test */
    public function it_verifies_payment_successfully()
    {
        $user = User::factory()->create();
        
        $paymentData = $this->service->initializePayment($user, 500, 'Test Payment');
        
        $verified = $this->service->verifyPayment(
            $paymentData['payment_id'],
            $paymentData['order_id'],
            'mock_signature_123'
        );

        $this->assertTrue($verified);

        $payment = Payment::where('payment_id', $paymentData['payment_id'])->first();
        $this->assertEquals('completed', $payment->payment_status);
        $this->assertNotNull($payment->payment_signature);
        $this->assertNotNull($payment->paid_at);
    }

    /** @test */
    public function it_returns_false_for_invalid_payment_verification()
    {
        $verified = $this->service->verifyPayment('invalid_pay_id', 'invalid_order_id');

        $this->assertFalse($verified);
    }

    /** @test */
    public function it_handles_payment_failure()
    {
        $user = User::factory()->create();
        
        $paymentData = $this->service->initializePayment($user, 500, 'Test Payment');
        
        $this->service->handlePaymentFailure($paymentData['payment_id'], 'Insufficient funds');

        $payment = Payment::where('payment_id', $paymentData['payment_id'])->first();
        
        $this->assertEquals('failed', $payment->payment_status);
        $this->assertEquals('Insufficient funds', $payment->failure_reason);
    }

    /** @test */
    public function it_gets_payment_by_id()
    {
        $user = User::factory()->create();
        
        $paymentData = $this->service->initializePayment($user, 500, 'Test Payment');
        
        $payment = $this->service->getPayment($paymentData['payment_id']);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($paymentData['payment_id'], $payment->payment_id);
    }

    /** @test */
    public function it_returns_null_for_non_existent_payment()
    {
        $payment = $this->service->getPayment('non_existent_id');

        $this->assertNull($payment);
    }

    /** @test */
    public function it_creates_full_refund()
    {
        $user = User::factory()->create();
        
        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'payment_status' => 'completed',
        ]);

        $refund = $this->service->createRefund($payment);

        $this->assertArrayHasKey('refund_id', $refund);
        $this->assertArrayHasKey('amount', $refund);
        $this->assertArrayHasKey('status', $refund);

        $this->assertEquals(1000, $refund['amount']);
        $this->assertEquals('processed', $refund['status']);
        $this->assertStringStartsWith('rfnd_', $refund['refund_id']);

        $payment->refresh();
        $this->assertEquals('refunded', $payment->refund_status);
        $this->assertEquals(1000, $payment->refund_amount);
        $this->assertNotNull($payment->refunded_at);
    }

    /** @test */
    public function it_creates_partial_refund()
    {
        $user = User::factory()->create();
        
        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'payment_status' => 'completed',
        ]);

        $refund = $this->service->createRefund($payment, 500);

        $this->assertEquals(500, $refund['amount']);

        $payment->refresh();
        $this->assertEquals(500, $payment->refund_amount);
    }

    /** @test */
    public function it_gets_user_payment_history()
    {
        $user = User::factory()->create();
        
        Payment::factory()->count(15)->create(['user_id' => $user->id]);
        
        $payments = $this->service->getUserPayments($user, 10);

        $this->assertCount(10, $payments);
        $this->assertInstanceOf(Payment::class, $payments->first());
    }

    /** @test */
    public function it_converts_amount_to_paise_for_razorpay()
    {
        $user = User::factory()->create();

        $result = $this->service->initializePayment($user, 499, 'Subscription');

        // Razorpay expects amount in paise (smallest currency unit)
        $this->assertEquals(49900, $result['amount']); // 499 * 100
    }

    /** @test */
    public function it_includes_metadata_in_payment()
    {
        $user = User::factory()->create();

        $metadata = [
            'subscription_id' => 123,
            'plan' => 'premium',
            'duration' => '1 month',
        ];

        $result = $this->service->initializePayment($user, 499, 'Subscription', $metadata);

        $payment = Payment::where('payment_id', $result['payment_id'])->first();

        $this->assertEquals($metadata, $payment->metadata);
    }
}
