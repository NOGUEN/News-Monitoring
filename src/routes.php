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
        $parsedUrl = parse_url($uri);
        $path = $parsedUrl['path'];
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

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
}