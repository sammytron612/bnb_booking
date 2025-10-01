<?php

use App\Services\PaymentServices\PaymentService;

// Test the PaymentService->processRefund method via artisan command

$paymentIntentId = 'pi_3SD5WjGvUTnoJDRl0JIwx3Yq'; // Real payment intent from your booking
$refundAmount = 20.00; // £20.00
$reason = 'Testing API refund flow';

echo "Testing PaymentService->processRefund():\n";
echo "Payment Intent ID: {$paymentIntentId}\n";
echo "Refund Amount: £{$refundAmount}\n";
echo "Reason: {$reason}\n\n";

try {
    $paymentService = app(PaymentService::class);
    $result = $paymentService->processRefund($paymentIntentId, $refundAmount, $reason);

    echo "Result:\n";
    print_r($result);

    if ($result['success']) {
        echo "\n✅ SUCCESS! Refund processed via Stripe API\n";
        echo "Refund ID: " . $result['refund_id'] . "\n";
        echo "Amount: £" . $result['amount'] . "\n";
        echo "\nNow check your webhook logs to see if Stripe sent the webhook!\n";
    } else {
        echo "\n❌ FAILED: " . $result['error'] . "\n";
    }

} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
}
