<?php
require_once __DIR__ . '/../models/AssignSemesterSubjectGrade.php';
require_once __DIR__ . '/../utils/response.php';

class AssignSemesterSubjectGradeController {
    public function getAllAssignSemesterSubjectGrades() {
        $assign_semester_subject_grade = new AssignSemesterSubjectGrade();
        $assign_semester_subject_grades = $assign_semester_subject_grade->fetchAll();
        echo jsonResponse(200, $assign_semester_subject_grades);
    }

    public function addAssignSemesterSubjectGrade($data) {
        $assign_semester_subject_grade = new AssignSemesterSubjectGrade();
        $result = $assign_semester_subject_grade->create($data);
        echo jsonResponse(201, ['message' => 'AssignSemesterSubjectGrade created successfully']);
    }

    public function updateAssignSemesterSubjectGrade($id, $data) {
        $assign_semester_subject_grade = new AssignSemesterSubjectGrade();
        $result = $assign_semester_subject_grade->update($id, $data);
        echo jsonResponse(200, ['message' => 'AssignSemesterSubjectGrade updated successfully']);
    }

    public function deleteAssignSemesterSubjectGrade($id) {
        $assign_semester_subject_grade = new AssignSemesterSubjectGrade();
        $result = $assign_semester_subject_grade->delete($id);
        echo jsonResponse(200, ['message' => 'AssignSemesterSubjectGrade deleted successfully']);
    }

    public function getAssignSemesterSubjectGradeById($id) {
        $assign_semester_subject_grade = new AssignSemesterSubjectGrade();
        $assign_semester_subject_grades = $assign_semester_subject_grade->fetchById($id);
        echo jsonResponse(200, $assign_semester_subject_grades);
    }
}