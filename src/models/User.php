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
                        user_type,
                        status,
                        created_date
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

            //check existing username
            $checkExiting = "SELECT * FROM tbl_user WHERE user_name = :user_name AND isDeleted = 0 AND status = 1";
            $stmt = $this->conn->prepare($checkExiting);
            $stmt->bindParam(':user_name', $data['user_name']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("Username already exists: " . $data['user_name']);
                return [
                    'status' => 'error',
                    'message' => 'Username already exists',
                    'code' => 'DUPLICATE_USERNAME'
                ];
            }
            

            // Insert new user
            $query = "INSERT INTO tbl_user (
                user_name, 
                password, 
                full_name, 
                phone, 
                user_type,
                isDeleted,
                status
            ) VALUES (
                :user_name,
                :password,
                :full_name,
                :phone,
                :user_type,
                0,
                1
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
                     AND isDeleted = 0 AND status = 1";
            
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
            
            //check existing username
            $checkExiting = "SELECT * FROM tbl_user WHERE user_name = :user_name AND isDeleted = 0 AND status = 1";
            $stmt = $this->conn->prepare($checkExiting);
            $stmt->bindParam(':user_name', $data['user_name']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                error_log("Username already exists: " . $data['user_name']);
                return [
                    'status' => 'error',
                    'message' => 'Username already exists',
                    'code' => 'DUPLICATE_USERNAME'
                ];
            }

            $updateFields = [];
            $params = [':user_id' => $userId];

            // Build dynamic update query
            if (isset($data['full_name'])) {
                $updateFields[] = "full_name = :full_name";
                $params[':full_name'] = $data['full_name'];
            }
            if (isset($data['phone'])) {
                $updateFields[] = "phone = :phone";
                $params[':phone'] = $data['phone'];
            }
            if (isset($data['user_type'])) {
                $updateFields[] = "user_type = :user_type";
                $params[':user_type'] = $data['user_type'];
            }
            if (isset($data['status'])) {
                $updateFields[] = "status = :status";
                $params[':status'] = $data['status'];
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
            $query = "UPDATE tbl_user SET isDeleted = 1, status = 0 WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in delete: " . $e->getMessage());
            throw $e;
        }
    }

    private function isUsernameExists($username, $excludeUserId = null) {
        try {
            $query = "SELECT COUNT(*) as count FROM tbl_user WHERE user_name = :user_name AND isDeleted = 0";
            if ($excludeUserId) {
                $query .= " AND user_id != :user_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_name', $username);
            if ($excludeUserId) {
                $stmt->bindParam(':user_id', $excludeUserId);
            }
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error checking username existence: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchById($userId) {
        try {
            $query = "SELECT user_id, full_name, user_name, phone, user_type, created_date 
                     FROM tbl_user 
                     WHERE user_id = :user_id 
                     AND isDeleted = 0 AND status = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in fetchById: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateStatus($userId, $status) {
        try {
            $query = "UPDATE tbl_user SET status = :status WHERE user_id = :user_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return ["error" => "Failed to update user status"];
            }
        } catch (PDOException $e) {
            error_log("Database Error in updateStatus: " . $e->getMessage());
            throw $e;
        }
    }

    public function authenticate($username, $password) {
        try {
            $query = "SELECT * FROM tbl_user WHERE user_name = :username AND isDeleted = 0 AND status = 1";
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

    public function searchUser($searchQuery) {
        try {
            $query = "SELECT * FROM tbl_user WHERE user_id LIKE :searchQuery OR user_name LIKE :searchQuery OR full_name LIKE :searchQuery OR phone LIKE :searchQuery AND isDeleted = 0 AND status = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':searchQuery', $searchQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in searchUser: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUser() {
        try {

            $query = "SELECT * FROM tbl_user WHERE user_type = 'user' AND isDeleted = 0 AND status = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $e) {
            error_log("Database Error in getUser: " . $e->getMessage());
            throw $e;
        }
    }

    public function count() {
        try {
            $query = "SELECT COUNT(*) as count FROM tbl_user WHERE user_type != 'admin' AND user_type != 'super_admin' AND isDeleted = 0 AND status = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Database Error in count: " . $e->getMessage());
            throw $e;
        }
    }

    // function for resetting password securely
    public function resetPassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // hash before storing
            $query = "UPDATE tbl_user SET password = :newPassword WHERE user_id = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':newPassword', $hashedPassword);
            $stmt->bindParam(':userId', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in resetPassword: " . $e->getMessage());
            throw $e;
        }
    }

    // function for changing password securely
    public function changePassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // hash before storing
            $query = "UPDATE tbl_user SET password = :newPassword WHERE user_id = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':newPassword', $hashedPassword);
            $stmt->bindParam(':userId', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in changePassword: " . $e->getMessage());
            throw $e;
        }
}
}
