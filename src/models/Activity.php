<?php
require_once __DIR__ . '/../config/database.php';

class Activity {
    private $conn;
    private $table_name = "tbl_activity_log";

    // Activity types
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_SCORE_ENTRY = 'score_entry';
    const TYPE_STUDENT_REGISTER = 'student_register';
    const TYPE_STUDENT_UPDATE = 'student_update';
    const TYPE_CLASS_CREATE = 'class_create';
    const TYPE_CLASS_UPDATE = 'class_update';
    const TYPE_SEMESTER_EXAM = 'semester_exam';
    const TYPE_REPORT_GENERATE = 'report_generate';

    // Activity categories
    const CATEGORY_AUTH = 'authentication';
    const CATEGORY_STUDENT = 'student_management';
    const CATEGORY_CLASS = 'class_management';
    const CATEGORY_SCORE = 'score_management';
    const CATEGORY_REPORT = 'report_management';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getRecentActivities($limit = 10, $category = null, $type = null) {
        try {
            $query = "SELECT a.*, u.full_name as user_name 
                     FROM " . $this->table_name . " a
                     LEFT JOIN tbl_user u ON a.user_id = u.user_id
                     WHERE a.isDeleted = 0";
            
            $params = [];
            
            if ($category) {
                $query .= " AND a.category = :category";
                $params[':category'] = $category;
            }
            
            if ($type) {
                $query .= " AND a.type = :type";
                $params[':type'] = $type;
            }
            
            $query .= " ORDER BY a.created_at DESC LIMIT :limit";
            $params[':limit'] = (int)$limit;

            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                if ($key === ':limit') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return array('error' => $e->getMessage());
        }
    }

    public function logActivity($type, $description, $user_id, $category, $details = null) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (type, description, user_id, category, details, created_at) 
                     VALUES (:type, :description, :user_id, :category, :details, NOW())";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':details', $details);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return array('error' => $e->getMessage());
        }
    }

    public function getActivityStats($startDate = null, $endDate = null) {
        try {
            $query = "SELECT 
                        category,
                        type,
                        COUNT(*) as count,
                        DATE(created_at) as date
                     FROM " . $this->table_name . "
                     WHERE isDeleted = 0";
            
            $params = [];
            
            if ($startDate) {
                $query .= " AND created_at >= :start_date";
                $params[':start_date'] = $startDate;
            }
            
            if ($endDate) {
                $query .= " AND created_at <= :end_date";
                $params[':end_date'] = $endDate;
            }
            
            $query .= " GROUP BY category, type, DATE(created_at)
                       ORDER BY date DESC, count DESC";

            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return array('error' => $e->getMessage());
        }
    }
}
?> 