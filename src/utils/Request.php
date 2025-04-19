<?php
class Request {
    private $data;

    public function __construct($data = []) {
        $this->data = $data;
    }

    public function getBody() {
        return $this->data;
    }

    public function getParam($key, $default = null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}
?> 