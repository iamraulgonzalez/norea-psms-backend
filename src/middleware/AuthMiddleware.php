<?php

require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/response.php';

class AuthMiddleware {
    public static function verifyAuth() {
        try {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';

            if (empty($authHeader)) {
                return false;
            }

            // Extract token from Bearer header
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
            } else {
                return false;
            }

            // Initialize JWT and verify token
            JWT::init();
            $decoded = JWT::verify($token);

            if (!$decoded) {
                return false;
            }

            return $decoded;
        } catch (Exception $e) {
            error_log("Error in AuthMiddleware: " . $e->getMessage());
            return false;
        }
    }

    public static function requireAuth() {
        $auth = self::verifyAuth();
        if (!$auth) {
            return jsonResponse(401, [
                'status' => 'error',
                'message' => 'Authentication required'
            ]);
        }
        return $auth;
    }
} 