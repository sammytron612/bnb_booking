<?php

// Test the refund flow
// We'll use booking ID 58 (0123958) that we saw in the webhook logs

$bookingId = 58; // Database ID, not booking reference
$refundAmount = 20.00;
$reason = "Testing refund webhook flow";

$url = "http://eileen_bnb.test/admin/bookings/{$bookingId}/refund";

$data = [
    'amount' => $refundAmount,
    'reason' => $reason
];

echo "Testing refund flow:\n";
echo "URL: {$url}\n";
echo "Amount: £{$refundAmount}\n";
echo "Reason: {$reason}\n\n";

// Use curl to call the refund endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: {$httpCode}\n";
echo "Response:\n{$response}\n";
