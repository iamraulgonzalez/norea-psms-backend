<?php
function jsonResponse($statusCode, $data) {
    http_response_code($statusCode);
    return json_encode($data);
}