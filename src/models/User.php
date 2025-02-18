<?php

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllUsers() {
        try {
            $query = "SELECT 
                        user_id as id,
                        user_name,
                        full_name,
                        phone,
                        user_type
                    FROM tbl_user 
                    WHERE isDeleted = 0 
                    ORDER BY user_id DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in getAllUsers: " . $e->getMessage());
            throw $e;
        }
    }

    public function register($data) {
        try {
            error_log("Starting user registration in model");

            // Check if username already exists
            $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_user WHERE user_name = :user_name AND isDeleted = 0");
            $checkStmt->bindParam(':user_name', $data['user_name']);
            $checkStmt->execute();
            
            if ($checkStmt->fetchColumn() > 0) {
                error_log("Username already exists: " . $data['user_name']);
                return false;
            }

            // Insert new user
            $query = "INSERT INTO tbl_user (
                user_name, 
                password, 
                full_name, 
                phone, 
                user_type,
                isDeleted
            ) VALUES (
                :user_name,
                :password,
                :full_name,
                :phone,
                :user_type,
                0
            )";

            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':user_name', $data['user_name']);
            $stmt->bindParam(':password', $data['password']);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':user_type', $data['user_type']);

            error_log("Executing registration query");
            $result = $stmt->execute();

            if (!$result) {
                error_log("Registration failed. SQL Error: " . json_encode($stmt->errorInfo()));
                return false;
            }

            error_log("User registered successfully");
            return true;

        } catch (PDOException $e) {
            error_log("Database Error in register: " . $e->getMessage());
            throw $e;
        }
    }

    public function login($username, $password) {
        try {
            error_log("Starting database login for user: " . $username);
    
            $query = "SELECT user_id, full_name, user_name, password, user_type, phone 
                     FROM tbl_user 
                     WHERE user_name = :user_name 
                     AND isDeleted = 0";
            
            error_log("Executing query: " . $query);
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_name', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Database returned user: " . ($user ? "Yes" : "No"));
            
            if (!$user) {
                error_log("No user found with username: " . $username);
                return ["error" => "Invalid username or password"];
            }
    
            error_log("Verifying password for user: " . $username);
            if (!password_verify($password, $user['password'])) {
                error_log("Password verification failed for user: " . $username);
                return ["error" => "Invalid username or password"];
            }
            
            error_log("Password verified successfully");
            unset($user['password']);
            return $user;
            
        } catch (PDOException $e) {
            error_log("Database Error in login: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            throw $e;
        }
    }

    public function update($userId, $data) {
        try {
            // Check if username exists for other users
            if (isset($data['user_name']) && $this->isUsernameExists($data['user_name'], $userId)) {
                return ["error" => "Username already exists"];
            }

            $updateFields = [];
            $params = [':user_id' => $userId];

            // Build dynamic update query
            if (isset($data['full_name'])) {
                $updateFields[] = "full_name = :full_name";
                $params[':full_name'] = $data['full_name'];
            }
            if (isset($data['user_name'])) {
                $updateFields[] = "user_name = :user_name";
                $params[':user_name'] = $data['user_name'];
            }
            if (isset($data['password'])) {
                $updateFields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            if (isset($data['phone'])) {
                $updateFields[] = "phone = :phone";
                $params[':phone'] = $data['phone'];
            }
            if (isset($data['user_type'])) {
                $updateFields[] = "user_type = :user_type";
                $params[':user_type'] = $data['user_type'];
            }

            if (empty($updateFields)) {
                return ["error" => "No fields to update"];
            }

            $query = "UPDATE tbl_user SET " . implode(", ", $updateFields) . " 
                     WHERE user_id = :user_id AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($userId) {
        try {
            // Soft delete - update isDeleted flag
            $query = "UPDATE tbl_user SET isDeleted = 1 WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in delete: " . $e->getMessage());
            throw $e;
        }
    }

    private function isUsernameExists($username, $excludeUserId = null) {
        $query = "SELECT COUNT(*) FROM tbl_user WHERE user_name = :user_name AND isDeleted = 0";
        if ($excludeUserId) {
            $query .= " AND user_id != :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_name', $username);
        if ($excludeUserId) {
            $stmt->bindParam(':user_id', $excludeUserId);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    public function fetchById($userId) {
        try {
            $query = "SELECT user_id, full_name, user_name, phone, user_type, create_date 
                     FROM tbl_user 
                     WHERE user_id = :user_id 
                     AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in fetchById: " . $e->getMessage());
            throw $e;
        }
    }

    public function authenticate($username, $password) {
        try {
            $query = "SELECT * FROM tbl_user WHERE user_name = :username AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                return [
                    'id' => $user['user_id'],
                    'user_name' => $user['user_name'],
                    'user_type' => $user['user_type'],
                    'full_name' => $user['full_name']
                ];
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            throw $e;
        }
    }
}
