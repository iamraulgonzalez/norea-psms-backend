<?php

class Response {
    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        return $this;
    }

    public function status($statusCode) {
        http_response_code($statusCode);
        return $this;
    }
}

function jsonResponse($statusCode, $data) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function errorResponse($statusCode, $message) {
    jsonResponse($statusCode, ['error' => $message]);
}

function successResponse($data = null) {
    jsonResponse(200, ['data' => $data]);
}