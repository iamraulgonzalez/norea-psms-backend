<?php

class Logger {
    private $logFile;
    private $context;
    
    public function __construct($context) {
        $this->context = $context;
        $logDir = __DIR__ . '/../../logs/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $this->logFile = $logDir . date('Y-m-d') . '.log';
    }
    
    public function info($message) {
        $this->log('INFO', $message);
    }
    
    public function error($message) {
        $this->log('ERROR', $message);
    }
    
    public function warning($message) {
        $this->log('WARNING', $message);
    }
    
    public function debug($message) {
        $this->log('DEBUG', $message);
    }
    
    private function log($level, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] [{$this->context}] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
} 