<?php
// Generate a secure random key
$key = bin2hex(random_bytes(32));
echo "Generated JWT Secret Key:\n";
echo $key . "\n";

// Optionally, create/update .env file
$envFile = __DIR__ . '/.env';
$envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

if (strpos($envContent, 'JWT_SECRET=') !== false) {
    // Replace existing JWT_SECRET
    $envContent = preg_replace('/JWT_SECRET=.*/', 'JWT_SECRET=' . $key, $envContent);
} else {
    // Add new JWT_SECRET
    $envContent .= "\nJWT_SECRET=" . $key;
}

file_put_contents($envFile, $envContent);
echo "\nKey has been saved to .env file"; 