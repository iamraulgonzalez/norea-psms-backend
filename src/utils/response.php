<?php

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