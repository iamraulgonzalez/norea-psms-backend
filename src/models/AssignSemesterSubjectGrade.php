<?php
require_once dirname(__DIR__) . '/config/Database.php';

class AssignSemesterSubjectGrade {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function fetchAll() {
        try {
            $query = "SELECT DISTINCT 
                        a.grade_id,
                        g.grade_name,
                        a.semester_id,
                        s.semester_name
                    FROM tbl_assign_semestersubject_grade a
                    LEFT JOIN tbl_grade g ON a.grade_id = g.grade_id
                    LEFT JOIN tbl_semester s ON a.semester_id = s.semester_id
                    WHERE a.isDeleted = 0
                    ORDER BY g.grade_id, s.semester_id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($assignments as $assignment) {
                $subjects = $this->getSubjectsGrouped(
                    $assignment['grade_id'],
                    $assignment['semester_id']
                );

                $result[] = [
                    'grade_id' => (int)$assignment['grade_id'],
                    'grade_name' => $assignment['grade_name'],
                    'semester_id' => (int)$assignment['semester_id'],
                    'semester_name' => $assignment['semester_name'],
                    'subjects' => $subjects
                ];
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Database Error in fetchAll: " . $e->getMessage());
            throw $e;
        }
    }

    private function getSubjectsGrouped($gradeId, $semesterId) {
        $query = "SELECT 
                    s.subject_code,
                    s.subject_name,
                    ss.sub_code,
                    ss.sub_subject_name
                FROM tbl_assign_semestersubject_grade a
                LEFT JOIN tbl_sub_subject ss ON a.sub_code = ss.sub_code
                LEFT JOIN tbl_subject s ON ss.subject_code = s.subject_code
                WHERE a.grade_id = :grade_id 
                AND a.semester_id = :semester_id
                AND a.isDeleted = 0
                ORDER BY s.subject_name, ss.sub_subject_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grade_id', $gradeId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by main subject
        $subjects = [];
        foreach ($rows as $row) {
            if (!isset($subjects[$row['subject_name']])) {
                $subjects[$row['subject_name']] = [
                    'subject_name' => $row['subject_name'],
                    'sub_subjects' => []
                ];
            }

            $subjects[$row['subject_name']]['sub_subjects'][] = [
                'sub_code' => (int)$row['sub_code'],
                'sub_subject_name' => $row['sub_subject_name']
            ];
        }

        return array_values($subjects);
    }

    public function fetchAllByGradeId($gradeId) {
        $query = "SELECT a.assign_semestersub_id, g.grade_name, ss.sub_subject_name, a.create_date FROM tbl_assign_semestersubject_grade a
                  LEFT JOIN tbl_grade g ON a.grade_id = g.grade_id
                  LEFT JOIN tbl_sub_subject ss ON a.sub_code = ss.sub_code
                  WHERE a.grade_id = :grade_id AND a.isDeleted = 0 ORDER BY a.create_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grade_id', $gradeId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO tbl_assign_semestersubject_grade 
                     (grade_id, sub_code, semester_id) 
                     VALUES (:grade_id, :sub_code, :semester_id)";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $data['grade_id']);
            $stmt->bindParam(':sub_code', $data['sub_code']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in create: " . $e->getMessage());
            throw $e;
        }
    }
   

    public function update($id, $data) {
        $query = "UPDATE tbl_assign_semestersubject_grade SET grade_id = :grade_id, sub_code = :sub_code WHERE assign_semstersub_id = :assign_semstersub_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grade_id', $id);
        $stmt->bindParam(':sub_code', $data['sub_code']);
        $stmt->bindParam(':assign_semstersub_id', $data['assign_semstersub_id']);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "UPDATE tbl_assign_semestersubject_grade SET isDeleted = 1 
                  WHERE assign_semstersub_id = :assign_semstersub_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':assign_semstersub_id', $id);
        return $stmt->execute();
    }
    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_assign_semestersubject_grade WHERE assign_semstersub_id = :assign_semstersub_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':assign_semstersub_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchSubjectsForGrade($gradeId, $semesterId, $yearStudyId) {
        $query = "SELECT 
                    g.grade_name,
                    s.subject_name,
                    ss.sub_subject_name,
                    sem.semester_name,
                    ys.year_study,
                    asg.hours_per_week
                  FROM tbl_assign_semestersubject_grade asg
                  JOIN tbl_grade g ON asg.grade_id = g.grade_id
                  JOIN tbl_sub_subject ss ON asg.sub_code = ss.sub_code
                  JOIN tbl_subject s ON ss.subject_code = s.subject_code
                  JOIN tbl_semester sem ON asg.semester_id = sem.semester_id
                  JOIN tbl_year_study ys ON asg.year_study_id = ys.year_study_id
                  WHERE asg.grade_id = :grade_id
                  AND asg.semester_id = :semester_id
                  AND asg.year_study_id = :year_study_id
                  AND asg.isDeleted = 0
                  ORDER BY s.subject_name, ss.sub_subject_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grade_id', $gradeId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->bindParam(':year_study_id', $yearStudyId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubjectsForClassBySemester($classId, $semesterId) {
        try {
            $query = "SELECT 
                        c.class_name,
                        g.grade_name,
                        sem.semester_name,
                        s.subject_name,
                        ss.sub_subject_name,
                        a.is_major
                    FROM tbl_classroom c
                    JOIN tbl_grade g ON c.grade_id = g.grade_id
                    JOIN tbl_assign_semestersubject_grade a ON g.grade_id = a.grade_id
                    JOIN tbl_sub_subject ss ON a.sub_code = ss.sub_code
                    JOIN tbl_subject s ON ss.subject_code = s.subject_code
                    JOIN tbl_semester sem ON a.semester_id = sem.semester_id
                    WHERE c.class_id = :class_id 
                    AND a.semester_id = :semester_id
                    AND c.isDeleted = 0
                    AND a.isDeleted = 0
                    ORDER BY s.subject_name, ss.sub_subject_name";
                    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId);
            $stmt->bindParam(':semester_id', $semesterId);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($rows)) {
                return null;
            }

            // Group by subjects
            $result = [
                'class_name' => $rows[0]['class_name'],
                'grade_name' => $rows[0]['grade_name'],
                'semester_name' => $rows[0]['semester_name'],
                'subjects' => []
            ];

            foreach ($rows as $row) {
                if (!isset($result['subjects'][$row['subject_name']])) {
                    $result['subjects'][$row['subject_name']] = [
                        'subject_name' => $row['subject_name'],
                        'sub_subjects' => []
                    ];
                }
                
                $result['subjects'][$row['subject_name']]['sub_subjects'][] = [
                    'sub_subject_name' => $row['sub_subject_name'],
                    'is_major' => (bool)$row['is_major']
                ];
            }

            $result['subjects'] = array_values($result['subjects']);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getSubjectsForClassBySemester: " . $e->getMessage());
            throw $e;
        }
    }
}
    