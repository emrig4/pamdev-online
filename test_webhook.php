<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$payload = [
    'event' => 'charge.success',
    'data' => [
        'reference' => 'TEST_REF_' . time(),
        'amount' => 150000, // ₦1,500 in kobo
        'currency' => 'NGN',
        'customer' => [
            'email' => 'edwinemerenu@gmail.com'
        ],
        'source' => [
            'source' => 'paystack'
        ],
        'plan' => null
    ]
];

echo "Testing webhook with payload:\n";
print_r($payload);
echo "\n";

try {
    $event = new App\Events\PaystackWebhookEvent($payload);
    event($event);
    echo "Webhook event triggered successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
