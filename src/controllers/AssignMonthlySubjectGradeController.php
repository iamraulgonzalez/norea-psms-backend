<?php
require_once __DIR__ . '/../models/AssignMonthlySubjectGrade.php';
require_once __DIR__ . '/../utils/response.php';

class AssignMonthlySubjectGradeController {

    public function getAllAssignMonthlySubjectGrades() {
        $assign_monthly_subject_grade = new AssignMonthlySubjectGrade();
        $assign_monthly_subject_grades = $assign_monthly_subject_grade->fetchAll();
        echo jsonResponse(200, $assign_monthly_subject_grades);
    }

    public function addAssignMonthlySubjectGrade($data) {
        $assign_monthly_subject_grade = new AssignMonthlySubjectGrade();
        $result = $assign_monthly_subject_grade->create($data);
        echo jsonResponse(201, ['message' => 'AssignMonthlySubjectGrade created successfully']);
    }

    public function updateAssignMonthlySubjectGrade($id, $data) {
        $assign_monthly_subject_grade = new AssignMonthlySubjectGrade();
        $result = $assign_monthly_subject_grade->update($id, $data);
        echo jsonResponse(200, ['message' => 'AssignMonthlySubjectGrade updated successfully']);
    }

    public function deleteAssignMonthlySubjectGrade($id) {
        $assign_monthly_subject_grade = new AssignMonthlySubjectGrade();
        $result = $assign_monthly_subject_grade->delete($id);
        echo jsonResponse(200, ['message' => 'AssignMonthlySubjectGrade deleted successfully']);
    }

    public function getAssignMonthlySubjectGradeById($id) {
        $assign_monthly_subject_grade = new AssignMonthlySubjectGrade();
        $assign_monthly_subject_grades = $assign_monthly_subject_grade->fetchById($id);
        echo jsonResponse(200, $assign_monthly_subject_grades);
    }
}