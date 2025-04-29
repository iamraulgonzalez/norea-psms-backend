<?php

class RoleMiddleware {
    public static function checkRole($requiredRole) {
        try {
            $auth = AuthMiddleware::verifyAuth();
            if (!$auth) {
                return false;
            }

            // Get user data from database
            $user = (new User())->fetchById($auth['user_id']);
            if (!$user) {
                return false;
            }

            // Check if user has required role
            if ($requiredRole === '*') {
                return true; // Super admin has all permissions
            }

            // Define role hierarchy
            $roleHierarchy = [
                'super_admin' => ['super_admin', 'admin', 'teacher', 'user'],
                'admin' => ['admin', 'teacher', 'user'],
                'teacher' => ['teacher', 'user'],
                'user' => ['user']
            ];

            // Check if user's role has permission
            return in_array($requiredRole, $roleHierarchy[$user['user_type']] ?? []);
        } catch (Exception $e) {
            error_log("Error in RoleMiddleware: " . $e->getMessage());
            return false;
        }
    }

    public static function requireRole($requiredRole) {
        if (!self::checkRole($requiredRole)) {
            return jsonResponse(403, [
                'status' => 'error',
                'message' => 'You do not have permission to access this resource'
            ]);
        }
        return true;
    }
} 