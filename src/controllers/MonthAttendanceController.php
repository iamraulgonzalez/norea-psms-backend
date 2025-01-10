<?php
require_once __DIR__ . '/../models/MonthAttendance.php';
require_once __DIR__ . '/../utils/response.php';

class MonthAttendanceController {
    public function getAllMonthAttendances() {
        $month_attendance = new MonthlyAttendance();
        $month_attendances = $month_attendance->fetchAll();
        echo jsonResponse(200, $month_attendances);
    }

    public function addMonthAttendance($data) {
        $month_attendance = new MonthlyAttendance();
        $result = $month_attendance->create($data);
        echo jsonResponse(201, ['message' => 'MonthAttendance created successfully']);
    }

    public function updateMonthAttendance($id, $data) {
        $month_attendance = new MonthlyAttendance();
        $result = $month_attendance->update($id, $data);
        echo jsonResponse(200, ['message' => 'MonthAttendance updated successfully']);
    }

    public function deleteMonthAttendance($id) {
        $month_attendance = new MonthlyAttendance();
        $result = $month_attendance->delete($id);
        echo jsonResponse(200, ['message' => 'MonthAttendance deleted successfully']);
    }

    public function getMonthAttendanceById($id) {
        $month_attendance = new MonthlyAttendance();
        $month_attendances = $month_attendance->fetchById($id);
        echo jsonResponse(200, $month_attendances);
    }

}