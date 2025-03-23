<?php
require_once __DIR__ . '/../config/database.php';
    class StudentSemesterScore {
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_semester_score WHERE isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        public function create($data) {
            $semester_id = isset($data['semester_id']) ? $data['semester_id'] : null;
            $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
            $score = isset($data['score']) ? $data['score'] : null;
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $student_id = isset($data['student_id']) ? $data['student_id'] : null;
            $grade_id = isset($data['grade_id']) ? $data['grade_id'] : null;
            $assign_semestersub_id = isset($data['assign_semestersub_id']) ? $data['assign_semestersub_id'] : null;
            $teacher_id = isset($data['teacher_id']) ? $data['teacher_id'] : null;
    
            if ($semester_id === null || $year_study_id === null || $score === null || $class_id === null || $student_id === null || $grade_id === null || $assign_semestersub_id === null || $teacher_id === null) {
                return false;
            }
    
            $query = "INSERT INTO tbl_semester_score (semester_id, year_study_id, score, class_id, student_id, grade_id, assign_semestersub_id, teacher_id) VALUES (:semester_id, :year_study_id, :score, :class_id, :student_id, :grade_id, :assign_semestersub_id, :teacher_id)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->bindParam(':year_study_id', $year_study_id);
            $stmt->bindParam(':score', $score);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':assign_semestersub_id', $assign_semestersub_id);
            $stmt->bindParam(':teacher_id', $teacher_id);
    
            return $stmt->execute();
        }
       
    
        public function update($id, $data) {
            $query = "UPDATE tbl_semester_score SET score = :score WHERE semester_score_id = :semester_score_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_score_id', $id);
            $stmt->bindParam(':score', $data['score']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "UPDATE tbl_semester_score SET isDeleted = 1 WHERE semester_score_id = :semester_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_score_id', $id);
            return $stmt->execute();
        }
        

        public function fetchById($id) {
            $query = "SELECT * FROM tbl_semester_score WHERE semester_score_id = :semester_score_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_score_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        /**
         * ទាញយកពិន្ទុសរុបឆមាស និងមធ្យមភាគចុងក្រោយសម្រាប់ថ្នាក់ជាក់លាក់
         * 
         * @param int $class_id អត្តលេខរបស់ថ្នាក់
         * @param int $semester_id អត្តលេខរបស់ឆមាស
         * @return array ទិន្នន័យពិន្ទុសរុប និងមធ្យមភាគចុងក្រោយរបស់សិស្សទាំងអស់ក្នុងថ្នាក់
         */
        public function getClassSemesterScores($class_id, $semester_id) {
            try {
                $query = "SELECT 
                            ssc.student_semester_score_id,
                            ssc.student_id,
                            si.student_name,
                            ssc.class_id,
                            ssc.semester_id,
                            sem.semester_name,
                            ssc.monthly_average,
                            ssc.months_counted,
                            ssc.semester_exam_score,
                            ssc.exam_subjects_count,
                            ssc.final_score,
                            ssc.calculation_note,
                            ssc.create_date
                        FROM tbl_student_semester_score ssc
                        JOIN tbl_student_info si ON ssc.student_id = si.student_id
                        JOIN tbl_semester sem ON ssc.semester_id = sem.semester_id
                        WHERE ssc.class_id = :class_id
                        AND ssc.semester_id = :semester_id
                        AND ssc.isDeleted = 0
                        ORDER BY ssc.final_score DESC";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':class_id', $class_id);
                $stmt->bindParam(':semester_id', $semester_id);
                $stmt->execute();
                
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            } catch (PDOException $e) {
                error_log("Error fetching class semester scores: " . $e->getMessage());
                return [
                    'status' => 'error',
                    'message' => 'Failed to fetch semester scores: ' . $e->getMessage()
                ];
            }
        }

        public function getClassSemesterExamScoresWithMonthly($class_id, $semester_id) {
            try {
                // First get all semester exam scores
                $query = "SELECT 
                    sses.id,
                    sses.student_id,
                    si.student_name,
                    sses.class_id,
                    sses.semester_id,
                    sses.semester_exam_subject_id,
                    sses.assign_subject_grade_id,
                    s.subject_name,
                    sses.score as exam_score,
                    sses.months_calculated,
                    sses.months_count
                FROM tbl_student_semester_exam_scores sses
                JOIN tbl_student_info si ON sses.student_id = si.student_id
                JOIN tbl_semester_exam_subjects ses ON sses.semester_exam_subject_id = ses.id
                JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
                JOIN tbl_subject s ON asg.subject_code = s.subject_code
                WHERE sses.class_id = :class_id
                AND sses.semester_id = :semester_id
                AND sses.isDeleted = 0";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':class_id', $class_id);
                $stmt->bindParam(':semester_id', $semester_id);
                $stmt->execute();
                
                $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // For each score, calculate monthly average if months are selected
                foreach ($scores as &$score) {
                    if (!empty($score['months_calculated']) && empty($score['monthly_average'])) {
                        $monthIds = explode(',', $score['months_calculated']);
                        
                        if (!empty($monthIds)) {
                            $placeholders = str_repeat('?,', count($monthIds) - 1) . '?';
                            
                            $avgQuery = "SELECT AVG(sms.score) as avg_score
                                        FROM tbl_student_monthly_score sms
                                        JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                                        WHERE sms.student_id = ?
                                        AND csms.class_id = ?
                                        AND csms.assign_subject_grade_id = ?
                                        AND csms.monthly_id IN ($placeholders)
                                        AND sms.isDeleted = 0";
                            
                            $params = array_merge(
                                [$score['student_id'], $score['class_id'], $score['assign_subject_grade_id']],
                                $monthIds
                            );
                            
                            $avgStmt = $this->conn->prepare($avgQuery);
                            $avgStmt->execute($params);
                            $result = $avgStmt->fetch(PDO::FETCH_ASSOC);
                            
                            $score['monthly_average'] = $result['avg_score'] ? floatval($result['avg_score']) : null;
                            
                            // Update the database with the calculated monthly average
                            $updateQuery = "UPDATE tbl_student_semester_exam_scores 
                                           SET monthly_average = ? 
                                           WHERE id = ?";
                            $updateStmt = $this->conn->prepare($updateQuery);
                            $updateStmt->execute([$score['monthly_average'], $score['id']]);
                        }
                    }
                }
                
                return [
                    'status' => 'success',
                    'data' => $scores
                ];
                
            } catch (PDOException $e) {
                error_log("Error in getClassSemesterExamScoresWithMonthly: " . $e->getMessage());
                return [
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                ];
            }
        }
    }
