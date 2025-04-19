<?php

class RoleMiddleware {
    private static $rolePermissions = [
        'super_admin' => ['*'], // Has access to everything
        'admin' => [
            'users.*',
            'students.*',
            'teachers.*',
            'subjects.*',
            'grades.*',
            'classrooms.*',
            'reports.*'
        ],
        'teacher' => [
            'students.view',
            'students.edit',
            'subjects.view',
            'grades.view',
            'classrooms.view',
            'reports.view'
        ],
        'user' => [
            'students.view',
            'subjects.view',
            'grades.view',
            'classrooms.view'
        ]
    ];

    public static function checkPermission($requiredPermission) {
        $auth = AuthMiddleware::verifyAuth();
        
        if (!$auth) {
            return false;
        }

        $userRole = $auth['user_type'];
        
        // Super admin has access to everything
        if ($userRole === 'super_admin') {
            return true;
        }

        // Check if user's role exists in permissions
        if (!isset(self::$rolePermissions[$userRole])) {
            return false;
        }

        // Check if user has the required permission
        $userPermissions = self::$rolePermissions[$userRole];
        
        // Check for wildcard permissions (e.g., 'users.*')
        foreach ($userPermissions as $permission) {
            if ($permission === '*') {
                return true;
            }
            
            // Check for wildcard at the end (e.g., 'users.*')
            if (str_ends_with($permission, '.*')) {
                $basePermission = substr($permission, 0, -2);
                if (str_starts_with($requiredPermission, $basePermission)) {
                    return true;
                }
            }
            
            // Exact match
            if ($permission === $requiredPermission) {
                return true;
            }
        }

        return false;
    }

    public static function requirePermission($requiredPermission) {
        if (!self::checkPermission($requiredPermission)) {
            jsonResponse(403, [
                'status' => 'error',
                'message' => 'You do not have permission to access this resource'
            ]);
            exit();
        }
    }
} 