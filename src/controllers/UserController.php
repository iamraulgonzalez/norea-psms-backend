<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function getAllUsers() {
        try {
            $auth = AuthMiddleware::verifyAuth();
            if (!$auth) {
                return jsonResponse(401, [
                    'status' => 'error',
                    'message' => 'Authentication required'
                ]);
            }

            if (!isset($auth['user_type']) || 
                ($auth['user_type'] !== 'admin' && $auth['user_type'] !== 'super_admin')) {
                return jsonResponse(403, [
                    'status' => 'error',
                    'message' => 'Admin privileges required'
                ]);
            }

            $users = $this->user->getAllUsers();
            return jsonResponse(200, [
                'status' => 'success',
                'data' => array_map(function($user) {
                    return [
                        'id' => $user['id'],
                        'user_name' => $user['user_name'],
                        'full_name' => $user['full_name'],
                        'phone' => $user['phone'],
                        'user_type' => $user['user_type'],
                        'status' => $user['status']
                    ];
                }, $users)
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllUsers: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ]);
        }
    }

    public function updateStatus($userId) {
        try {
            $auth = AuthMiddleware::verifyAuth();
            if (!$auth) {
                return jsonResponse(401, [
                    'status' => 'error',
                    'message' => 'Authentication required'
                ]);
            }

            if (!isset($auth['user_type']) || 
                ($auth['user_type'] !== 'admin' && $auth['user_type'] !== 'super_admin')) {
                return jsonResponse(403, [
                    'status' => 'error',
                    'message' => 'Admin privileges required'
                ]);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['status'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Status is required'
                ]);
            }

            $result = $this->user->updateStatus($userId, $data['status']);
            
            if (isset($result['error'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => $result['error']
                ]);
            }

            return jsonResponse(200, [
                'status' => 'success',
                'message' => 'User status updated successfully'
            ]);
        } catch (Exception $e) {
            error_log("Error updating user status: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to update user status'
            ]);
        }
    }

    public function register() {
        try {
            error_log("Starting registration process");
            
            // Get and decode the request body
            $data = json_decode(file_get_contents('php://input'), true);
            error_log("Received registration data: " . json_encode($data));

            // Validate required fields
            if (!isset($data['user_name']) || !isset($data['password']) || !isset($data['full_name'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ]);
            }

            // Hash the password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Attempt to create the user
            $result = $this->user->register($data);

            if ($result) {
                return jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'User registered successfully'
                ]);
            } else {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Failed to register user'
                ]);
            }

        } catch (PDOException $e) {
            error_log("Database Error in register: " . $e->getMessage());
            if ($e->getCode() == 23000) { // Duplicate entry
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Username already exists'
                ]);
            }
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Database error occurred'
            ]);
        } catch (Exception $e) {
            error_log("Error in register: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to register user',
                'details' => $e->getMessage()
            ]);
        }
    }

    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_name']) || !isset($data['password'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Username and password are required'
                ]);
            }

            $user = $this->user->authenticate($data['user_name'], $data['password']);
            
            if (!$user) {
                return jsonResponse(401, [
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ]);
            }

            error_log("Authenticated user: " . json_encode($user));
            
            $token = $this->generateToken($user);

            return jsonResponse(200, [
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ]);
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error during login'
            ]);
        }
    }

    private function generateToken($user) {
        JWT::init(); // Initialize with default key
        $payload = [
            'user_id' => $user['id'],
            'user_name' => $user['user_name'],
            'user_type' => $user['user_type'],
            'exp' => time() + (60 * 60 * 24)
        ];
        return JWT::encode($payload);
    }

    public function update($userId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->user->update($userId, $data);
            
            if (isset($result['error'])) {
                jsonResponse(400, $result);
                return;
            }

            jsonResponse(200, ['message' => 'User updated successfully']);
        } catch (Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            jsonResponse(500, ['error' => 'Failed to update user']);
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        setcookie('PHPSESSID', '', time() - 3600, '/');
        jsonResponse(200, ['message' => 'Logged out successfully']);
    }

    public function getCurrentUser() {
        if (!AuthMiddleware::verifyAuth()) {
            jsonResponse(401, ['error' => 'Not authenticated']);
            return;
        }
    
        if (isset($_SESSION['user'])) {
            jsonResponse(200, $_SESSION['user']);
        } else {
            jsonResponse(401, ['error' => 'Not logged in']);
        }
    }

    public function verifyToken() {
        try {
            // The authenticate middleware will handle token verification
            // If we reach here, token is valid
            return jsonResponse(200, [
                'status' => 'success',
                'message' => 'Token is valid'
            ]);
        } catch (Exception $e) {
            return jsonResponse(401, [
                'status' => 'error',
                'message' => 'Invalid token'
            ]);
        }
    }
}
