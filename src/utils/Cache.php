<?php

class Cache {
    private $cacheDir;
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../../cache/';
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }
    
    public function get($key) {
        $filename = $this->getCacheFilename($key);
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = file_get_contents($filename);
        $cache = unserialize($data);
        
        if ($cache['expires'] < time()) {
            unlink($filename);
            return null;
        }
        
        return $cache['data'];
    }
    
    public function set($key, $value, $ttl = 300) {
        $filename = $this->getCacheFilename($key);
        $cache = [
            'data' => $value,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($filename, serialize($cache));
    }
    
    public function delete($key) {
        $filename = $this->getCacheFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    private function getCacheFilename($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
} 