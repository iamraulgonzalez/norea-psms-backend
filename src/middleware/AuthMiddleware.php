<?php

require_once __DIR__ . '/../utils/JWT.php';

class AuthMiddleware {
    public static function verifyAuth() {
        try {
            $headers = getallheaders();
            error_log("Auth Headers: " . json_encode($headers));

            if (!isset($headers['Authorization']) && !isset($headers['authorization'])) {
                error_log("No Authorization header found");
                return false;
            }

            // Handle case-insensitive header
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];
            $token = str_replace('Bearer ', '', $authHeader);
            
            if (empty($token)) {
                error_log("Empty token");
                return false;
            }

            error_log("Attempting to verify token: " . substr($token, 0, 20) . "...");
            
            JWT::init();
            $decoded = JWT::verify($token);
            
            error_log("Decoded token: " . json_encode($decoded));
            
            if (!$decoded || !isset($decoded['user_type'])) {
                error_log("Invalid token structure or missing user_type");
                return false;
            }

            error_log("Token verified successfully for user: " . json_encode($decoded));
            return $decoded;
            
        } catch (Exception $e) {
            error_log("Auth error: " . $e->getMessage());
            return false;
        }
    }

    public static function authenticate() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            jsonResponse(401, ['error' => 'No token provided']);
            exit();
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        try {
            // Verify your JWT token here
            // You can use Firebase JWT or your preferred JWT library
            return true;
        } catch (Exception $e) {
            jsonResponse(401, ['error' => 'Invalid token']);
            exit();
        }
    }
} 