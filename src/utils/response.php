<?php
function jsonResponse($statusCode, $data) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    return json_encode($data);
}