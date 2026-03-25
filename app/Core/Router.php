<?php

namespace App\Core;

class Router {
    protected array $routes = [];

    public function get($path, $callback) {
        $path = rtrim($path, '/') ?: '/';
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $path = rtrim($path, '/') ?: '/';
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch(Request $request) {
        $method = $request->getMethod();
        $path = rtrim($request->getPath(), '/') ?: '/';
        
        // Basic match (can be improved later with regex)
        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            Response::error("Route not found: $path", 404);
        }

        if (is_array($callback)) {
            $controllerName = $callback[0];
            $methodName = $callback[1];
            $controller = new $controllerName();
            return $controller->$methodName($request);
        }

        return call_user_func($callback, $request);
    }
}
