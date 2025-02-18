<?php
session_start();

// CORS Headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Expose-Headers: Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/routers/api.php';

// Get the request URI and remove query parameters
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove base path from URI if needed
$basePath = '/api'; // Adjust this if your API is mounted at a different path
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

try {
    route($uri, $method);
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}