<?php
require_once __DIR__ . '/../config/database.php';

class Classroom {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT c.*, s.session_name, g.grade_name, u.full_name as teacher_name, u.user_id as teacher_id,
                  c.year_study_id, ys.year_study, c.status, c.num_students_in_class, c.create_date,
                  (SELECT COUNT(*) FROM tbl_study st WHERE st.class_id = c.class_id AND st.status = 'active' AND st.isDeleted = 0) as actual_student_count
                  FROM tbl_classroom c
                  INNER JOIN tbl_school_session s ON c.session_id = s.session_id 
                  INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                  LEFT JOIN tbl_user u ON c.teacher_id = u.user_id
                  LEFT JOIN tbl_year_study ys ON c.year_study_id = ys.year_study_id
                  WHERE c.isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $class_name = $data['class_name'] ?? null;
            $grade_id = $data['grade_id'] ?? null;
            $session_id = $data['session_id'] ?? null;
            $teacher_id = $data['teacher_id'] ?? null;
            $num_students_in_class = $data['num_students_in_class'] ?? 45;
            $year_study_id = $data['year_study_id'] ?? null;
            $status = $data['status'] ?? 'active';

            // Validate required fields
            if ($class_name === null || $grade_id === null || $session_id === null) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ];
            }

            // First check if this class name already exists in any session
            $checkExistingClass = "SELECT s.session_name 
                                 FROM tbl_classroom c
                                 JOIN tbl_school_session s ON c.session_id = s.session_id
                                 WHERE c.class_name = :class_name 
                                 AND c.grade_id = :grade_id
                                 AND c.isDeleted = 0
                                 LIMIT 1";
            
            $stmt = $this->conn->prepare($checkExistingClass);
            $stmt->bindParam(':class_name', $class_name);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->execute();
            
            if ($existingClass = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return [
                    'status' => 'error',
                    'message' => "ថ្នាក់ " . $class_name . " " . $existingClass['session_name'] . " មានរួចហើយ"
                ];
            }

            // Insert new class
            $insertQuery = "INSERT INTO tbl_classroom (class_name, grade_id, session_id, teacher_id, num_students_in_class, year_study_id, status) 
                           VALUES (:class_name, :grade_id, :session_id, :teacher_id, :num_students_in_class, :year_study_id, :status)";
            $stmt = $this->conn->prepare($insertQuery);

            $stmt->bindParam(':class_name', $class_name);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->bindParam(':teacher_id', $teacher_id);
            $stmt->bindParam(':num_students_in_class', $num_students_in_class, PDO::PARAM_INT);
            $stmt->bindParam(':year_study_id', $year_study_id);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Class created successfully',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to create class'
            ];

        } catch (PDOException $e) {
            error_log("Database Error in create: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function update($id, $data) {
        try {
            $class_name = $data['class_name'] ?? null;
            $grade_id = $data['grade_id'] ?? null;
            $session_id = $data['session_id'] ?? null;
            $teacher_id = $data['teacher_id'] ?? null;
            $num_students_in_class = $data['num_students_in_class'] ?? 45;
            $year_study_id = $data['year_study_id'] ?? null;
            $status = $data['status'] ?? 'active';

            if ($class_name === null || $grade_id === null || $session_id === null || $num_students_in_class === null) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ];
            }

            // Check current student count
            $currentStudentsQuery = "SELECT COUNT(*) as count 
                                   FROM tbl_study
                                   WHERE class_id = :id 
                                   AND status = 'active'
                                   AND isDeleted = 0";
            $stmt = $this->conn->prepare($currentStudentsQuery);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Don't allow setting capacity below current student count
            if ($currentCount > $num_students_in_class) {
                return [
                    'status' => 'error',
                    'message' => "មិនអាចកំណត់ចំនួនសិស្សតិចជាងចំនួនសិស្សដែលមានស្រាប់ ($currentCount)"
                ];
            }

            // Check if this class name exists in any other session
            $checkExistingClass = "SELECT s.session_name 
                                 FROM tbl_classroom c
                                 JOIN tbl_school_session s ON c.session_id = s.session_id
                                 WHERE c.class_name = :class_name 
                                 AND c.grade_id = :grade_id 
                                 AND c.class_id != :id
                                 AND c.isDeleted = 0
                                 LIMIT 1";
            
            $stmt = $this->conn->prepare($checkExistingClass);
            $stmt->bindParam(':class_name', $class_name);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($existingClass = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return [
                    'status' => 'error',
                    'message' => "This class already exists in " . $existingClass['session_name'] . " session"
                ];
            }

            // Update the class
            $updateQuery = "UPDATE tbl_classroom 
                           SET class_name = :class_name, 
                               grade_id = :grade_id, 
                               session_id = :session_id,
                               teacher_id = :teacher_id,
                               num_students_in_class = :num_students_in_class,
                               year_study_id = :year_study_id,
                               status = :status
                           WHERE class_id = :class_id 
                           AND isDeleted = 0";

            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(':class_id', $id);
            $stmt->bindParam(':class_name', $class_name);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->bindParam(':teacher_id', $teacher_id);
            $stmt->bindParam(':num_students_in_class', $num_students_in_class, PDO::PARAM_INT);
            $stmt->bindParam(':year_study_id', $year_study_id);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Class updated successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to update class'
            ];

        } catch (PDOException $e) {
            error_log("Database Error in update: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }
    
    public function delete($id) {
        $query = "UPDATE tbl_classroom SET isDeleted = 1 WHERE class_id = :class_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $id);
        return $stmt->execute();
    }
    
    public function fetchById($id) {
        $query = "SELECT c.*, s.session_name, g.grade_name, t.teacher_name, t.teacher_id,
                  num_students_in_class,c.create_date
                  FROM tbl_classroom c 
                  INNER JOIN tbl_school_session s ON c.session_id = s.session_id 
                  INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                  LEFT JOIN tbl_teacher t ON c.teacher_id = t.teacher_id
                  WHERE c.class_id = :class_id AND c.isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getClassesByGrade($gradeId) {
        $query = "SELECT c.*, s.session_name, g.grade_name, u.full_name as teacher_name, u.user_id as teacher_id,
                  c.year_study_id, ys.year_study, c.status, c.num_students_in_class, c.create_date
                  FROM tbl_classroom c
                  INNER JOIN tbl_school_session s ON c.session_id = s.session_id 
                  INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                  LEFT JOIN tbl_user u ON c.teacher_id = u.user_id
                  LEFT JOIN tbl_year_study ys ON c.year_study_id = ys.year_study_id
                  WHERE c.grade_id = :grade_id AND c.isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grade_id', $gradeId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM tbl_classroom 
                     WHERE isDeleted = 0";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error in getCount: " . $e->getMessage());
            throw $e;
        }
    }

    public function getClassesByGradeAndSession($gradeId, $sessionId) {
        try {
            $query = "SELECT 
                        c.*,
                        g.grade_name,
                        s.session_name,
                        u.full_name,
                        u.user_id,
                        num_students_in_class,
                        c.create_date
                      FROM tbl_classroom c
                      INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                      INNER JOIN tbl_school_session s ON c.session_id = s.session_id
                      LEFT JOIN tbl_user u ON c.user_id = u.user_id
                      WHERE c.grade_id = :grade_id 
                      AND c.session_id = :session_id
                      AND c.isDeleted = 0
                      ORDER BY c.class_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $gradeId);
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
            
            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching classes: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch classes'
            ];
        }
    }

    public function getUsersByClassId($classId) {
        try {
            $query = "SELECT 
                        u.full_name
                      FROM tbl_user u
                      INNER JOIN tbl_classroom c ON u.user_id = c.teacher_id
                      WHERE c.class_id = :class_id
                      AND c.isDeleted = 0
                      AND u.isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ];
        }
    } 
    
}