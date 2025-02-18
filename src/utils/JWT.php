<?php

class JWT {
    private static $secretKey;

    public static function init($key = 'your-secret-key-here') {
        self::$secretKey = $key;
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $header = self::base64UrlEncode($header);
        
        $payload = json_encode($payload);
        $payload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', "$header.$payload", self::$secretKey, true);
        $signature = self::base64UrlEncode($signature);
        
        return "$header.$payload.$signature";
    }

    public static function verify($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        
        $header = $parts[0];
        $payload = $parts[1];
        
        // First verify signature
        $valid_signature = self::base64UrlEncode(hash_hmac('sha256', 
            "$header.$payload", 
            self::$secretKey, 
            true
        ));
        
        if (!hash_equals($parts[2], $valid_signature)) {
            return false;
        }
        
        // Then decode and verify payload
        $decoded = json_decode(self::base64UrlDecode($payload), true);
        if (!$decoded) return false;
        
        // Check expiration
        if (isset($decoded['exp']) && $decoded['exp'] < time()) {
            return false;
        }
        
        return $decoded;
    }
} 