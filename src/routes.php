<?php

use src\Controllers\NewsController;
use src\Controllers\ApiController;

class Router {
    private $routes = [];

    public function get($pattern, $callback) {
        $this->routes['GET'][$pattern] = $callback;
    }

    public function dispatchURI($uri) {
        $method = $_SERVER['REQUEST_METHOD'];
        $this->setCORSHeaders();

        if ($method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $parsedUrl = parse_url($uri);
        $path = $parsedUrl['path'];
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

        if (!isset($this->routes[$method])) {
            http_response_code(405);
            echo '405 Method Not Allowed';
            return;
        }

        foreach ($this->routes[$method] as $pattern => $callback) {
            $pattern = str_replace(['{', '}'], ['(?P<', '>[^/]+)'], $pattern);
            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_int($key)) unset($matches[$key]);
                }
                $_GET = array_merge($_GET, $this->parseQueryString($query));
                return call_user_func_array($callback, $matches);
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }

    private function parseQueryString($queryString) {
        $queryParams = [];
        parse_str($queryString, $queryParams);
        return $queryParams;
    }

    private function setCORSHeaders() {
        header("Access-Control-Allow-Origin: *"); // Allow all origins, adjust as needed
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }
}