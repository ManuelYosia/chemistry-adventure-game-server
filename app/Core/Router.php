<?php

namespace App\Core;

class Router {
    protected array $routes = [];
    protected array $middleware = [];

    public function get($path, $callback) {
        $path = rtrim($path, '/') ?: '/';
        $this->routes['GET'][$path] = $callback;
        return $this; // For chaining
    }

    public function post($path, $callback) {
        $path = rtrim($path, '/') ?: '/';
        $this->routes['POST'][$path] = $callback;
        return $this; // For chaining
    }

    public function middleware($middleware) {
        // Get the last registered route
        $methods = array_keys($this->routes);
        $lastMethod = end($methods);
        $paths = array_keys($this->routes[$lastMethod]);
        $lastPath = end($paths);

        if (!isset($this->middleware[$lastMethod][$lastPath])) {
            $this->middleware[$lastMethod][$lastPath] = [];
        }
        
        if (is_array($middleware)) {
            $this->middleware[$lastMethod][$lastPath] = array_merge($this->middleware[$lastMethod][$lastPath], $middleware);
        } else {
            $this->middleware[$lastMethod][$lastPath][] = $middleware;
        }

        return $this;
    }

    public function dispatch(Request $request) {
        $method = $request->getMethod();
        $path = rtrim($request->getPath(), '/') ?: '/';
        
        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            Response::error("Route not found: $path", 404);
        }

        // Execute Middleware
        $middlewares = $this->middleware[$method][$path] ?? [];
        foreach ($middlewares as $middlewareClass) {
            $middleware = new $middlewareClass();
            $middleware->handle($request);
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
