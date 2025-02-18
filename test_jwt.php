<?php
require_once __DIR__ . '/src/utils/JWT.php';

// Initialize JWT
JWT::init();

// Test data
$testPayload = [
    'user_id' => 1,
    'user_name' => 'test_user',
    'user_type' => 'admin'
];

// Generate a token
try {
    $token = JWT::generate($testPayload);
    echo "Generated Token:\n$token\n\n";

    // Verify the token
    $decoded = JWT::verify($token);
    echo "Decoded Payload:\n";
    print_r($decoded);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 