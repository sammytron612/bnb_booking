<?php

/*
 * STRIPE REFUND WEBHOOK TEST PAYLOAD
 *
 * Use this payload to test the charge.refunded webhook endpoint
 * POST to: https://your-domain.com/stripe/webhook
 *
 * Make sure to:
 * 1. Replace "pi_test_xxxxx" with an actual payment_intent_id from your bookings table
 * 2. Set proper Stripe-Signature header for webhook verification
 * 3. Adjust amounts as needed (amounts are in cents)
 */

$testWebhookPayload = [
    "id" => "evt_test_webhook",
    "object" => "event",
    "api_version" => "2020-08-27",
    "created" => time(),
    "data" => [
        "object" => [
            "id" => "ch_test_refund",
            "object" => "charge",
            "amount" => 30000, // £300.00 in pence
            "amount_refunded" => 6000, // £60.00 refunded in pence
            "payment_intent" => "pi_test_xxxxx", // Replace with actual payment_intent_id
            "refunded" => true,
            "refunds" => [
                "object" => "list",
                "data" => [
                    [
                        "id" => "re_test_refund",
                        "object" => "refund",
                        "amount" => 6000,
                        "created" => time(),
                        "currency" => "gbp",
                        "metadata" => [],
                        "reason" => "requested_by_customer",
                        "status" => "succeeded"
                    ]
                ]
            ]
        ]
    ],
    "livemode" => false,
    "pending_webhooks" => 1,
    "request" => [
        "id" => "req_test_refund",
        "idempotency_key" => null
    ],
    "type" => "charge.refunded"
];

echo "Test payload for charge.refunded webhook:\n";
echo json_encode($testWebhookPayload, JSON_PRETTY_PRINT);
echo "\n\nTo test this webhook, send this payload to your webhook endpoint with proper Stripe signature verification.\n";
