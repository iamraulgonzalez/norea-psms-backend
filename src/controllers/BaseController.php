<?php

class BaseController {
    protected function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $statusCode < 400 ? 'success' : 'error',
            'data' => $data
        ]);
        exit;
    }
    
    protected function sendError($statusCode, $message) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
} 