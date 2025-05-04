<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function getAllUsers() {
        try {
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
                        'created_date' => $user['created_date'],
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

    public function getUserById($userId) {
        try {
            $user = $this->user->fetchById($userId);
            return jsonResponse(200, [
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch user'
            ]);
        }
    }

    public function updateStatus($userId) {
        try {
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
            
            $data = json_decode(file_get_contents('php://input'), true);
            error_log("Received registration data: " . json_encode($data));

            if (!isset($data['user_name']) || !isset($data['password']) || !isset($data['full_name'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ]);
            }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $result = $this->user->register($data);

            if (is_array($result) && isset($result['status']) && $result['status'] === 'error') {
                return jsonResponse(400, $result);
            }

            if ($result === true) {
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
            if ($e->getCode() == 23000) {
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
                    'message' => 'Invalid username or password'
                ]);
            }

            // Generate JWT token
            JWT::init();
            $token = JWT::encode($user);

           
            return jsonResponse(200, [
                'status' => 'success',
                'token' => $token,
                'user' => $user
            ]);
        } catch (Exception $e) {
            error_log("Error in login: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to login'
            ]);
        }
    }

    public function update($userId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->user->update($userId, $data);
            
            if (isset($result['status']) && $result['status'] === 'error') {
                return jsonResponse(400, $result);
            }

            return jsonResponse(200, ['message' => 'User updated successfully']);
        } catch (Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            return jsonResponse(500, ['error' => 'Failed to update user']);
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        setcookie('PHPSESSID', '', time() - 3600, '/');
        jsonResponse(200, ['message' => 'Logged out successfully']);
    }

    public function getCurrentUser() {
        try {
            $auth = AuthMiddleware::verifyAuth();
            if (!$auth) {
                return jsonResponse(401, [
                    'status' => 'error',
                    'message' => 'Authentication required'
                ]);
            }

            // Get user data from database using the user_id from token
            $user = $this->user->fetchById($auth['user_id']);
            
            if (!$user) {
                return jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'User not found'
                ]);
            }

            // Add permissions to user data
            $rolePermissions = [
                'super_admin' => ['*'],
                'admin' => [
                    'users.*',
                    'students.*',
                    'teachers.*',
                    'subjects.*',
                    'grades.*',
                    'classrooms.*',
                    'reports.*',
                ],
                'teacher' => [
                    'students.view',
                    'students.edit',
                    'subjects.view',
                    'grades.view',
                    'classrooms.view',
                    'reports.view',
                ],
                'user' => [
                    'students.view',
                    'subjects.view',
                    'grades.view',
                    'classrooms.view',
                ],
            ];

            $user['permissions'] = $rolePermissions[$user['user_type']] ?? [];

            return jsonResponse(200, [
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            error_log("Error in getCurrentUser: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch current user'
            ]);
        }
    }

    public function verifyToken() {
        try {
            $auth = AuthMiddleware::verifyAuth();
            if (!$auth) {
                return jsonResponse(401, [
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ]);
            }

            // Get fresh user data
            $user = $this->user->fetchById($auth['user_id']);
            if (!$user) {
                return jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'User not found'
                ]);
            }

            // Remove sensitive data
            unset($user['password']);

            return jsonResponse(200, [
                'status' => 'success',
                'user' => $user
            ]);
        } catch (Exception $e) {
            error_log("Error in verifyToken: " . $e->getMessage());
            return jsonResponse(401, [
                'status' => 'error',
                'message' => 'Token verification failed'
            ]);
        }
    }

    public function searchUser($searchQuery) {
        try {
            $users = $this->user->searchUser($searchQuery);
            return jsonResponse(200, [
                'status' => 'success',
                'data' => $users
            ]);
        } catch (Exception $e) {
            error_log("Error in searchUser: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to search users'
            ]);
        }
    }

    public function getUser($user_id) {
        try {
            $users = $this->user->getUser($user_id);
            return jsonResponse(200, [
                'status' => 'success',
                'data' => $users
            ]);
        } catch (Exception $e) {
            error_log("Error in getUser: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to get users'
            ]);
        }
    }

    public function count() {
        try {
            $count = $this->user->count();
            return jsonResponse(200, [
                'status' => 'success',
                'data' => $count
            ]);
        } catch (Exception $e) {
            error_log("Error in count: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to count users'
            ]);
        }
    }

    public function resetPassword($userId, $newPassword){
        try{
            // Validate input
            if (empty($userId) || empty($newPassword)) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'អត្តលេខអ្នកប្រើប្រាស់ និងពាក្យសម្ងាត់ថ្មីត្រូវការ'
                ]);
            }

            $result = $this->user->resetPassword($userId, $newPassword);
            if (isset($result['error'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => $result['error']
                ]);
            }

            if (isset($result['success'])) {
                return jsonResponse(200, [
                    'status' => 'success',
                    'message' => $result['success']
                ]);
            }

            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to change password'
            ]);
        }
        catch (Exception $e) {
            error_log("Error in resetPassword: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to change password'
            ]);
        }
    }

    public function changePassword($userId, $oldPassword, $newPassword) {
        try {
            $result = $this->user->changePassword($userId, $oldPassword, $newPassword);
            
            if (isset($result['error'])) {
                return jsonResponse(400, [
                    'status' => 'error',
                    'message' => $result['error']
                ]);
            }

            if (isset($result['success'])) {
                return jsonResponse(200, [
                    'status' => 'success',
                    'message' => $result['success']
                ]);
            }

            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to change password'
            ]);
        }
        catch (Exception $e) {
            error_log("Error in changePassword: " . $e->getMessage());
            return jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to change password'
            ]);
        }
    }
}
