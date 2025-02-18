// Create new file: middleware/AuthMiddleware.php
<?php

class AuthMiddleware {
    public static function verifyAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check session first
        if (isset($_SESSION['user']) && isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
            $payload = JWT::verify($token);
            
            if ($payload && $payload['user_id'] === $_SESSION['user']['user_id']) {
                return true;
            }
        }

        // Check Authorization header
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? 
            str_replace('Bearer ', '', $headers['Authorization']) : null;

        if ($token) {
            $payload = JWT::verify($token);
            if ($payload) {
                return true;
            }
        }

        return false;
    }
}