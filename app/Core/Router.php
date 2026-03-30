<?php

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $middleware = [];
    protected ?string $lastMethod = null;
    protected ?string $lastPath = null;

    public function get($path, $callback)
    {
        $path = rtrim($path, '/') ?: '/';
        $this->routes['GET'][$path] = $callback;
        $this->lastMethod = 'GET';
        $this->lastPath = $path;
        return $this; // For chaining
    }

    public function post($path, $callback)
    {
        $path = rtrim($path, '/') ?: '/';
        $this->routes['POST'][$path] = $callback;
        $this->lastMethod = 'POST';
        $this->lastPath = $path;
        return $this; // For chaining
    }

    public function middleware($middleware)
    {
        if (!$this->lastMethod || !$this->lastPath) {
            return $this;
        }

        if (!isset($this->middleware[$this->lastMethod][$this->lastPath])) {
            $this->middleware[$this->lastMethod][$this->lastPath] = [];
        }

        if (is_array($middleware)) {
            $this->middleware[$this->lastMethod][$this->lastPath] = array_merge($this->middleware[$this->lastMethod][$this->lastPath], $middleware);
        } else {
            $this->middleware[$this->lastMethod][$this->lastPath][] = $middleware;
        }

        return $this;
    }

    public function dispatch(Request $request)
    {
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
