<?php
class BaseController {
    protected function sendError($message) {
        echo json_encode(['message' => $message]);
    }
} 