<?php
function jsonResponse($status, $data) {
    http_response_code($status);
    header('Content-Type: application/json');
    return json_encode($data);
}