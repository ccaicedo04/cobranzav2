<?php

namespace Core;

use Closure;
use Exception;

class Router
{
    private array $routes = [];

    public function get(string $route, array|Closure $action): void
    {
        $this->addRoute('GET', $route, $action);
    }

    public function post(string $route, array|Closure $action): void
    {
        $this->addRoute('POST', $route, $action);
    }

    public function match(array $methods, string $route, array|Closure $action): void
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $route, $action);
        }
    }

    private function addRoute(string $method, string $route, array|Closure $action): void
    {
        $this->routes[$method][$route] = $action;
    }

    public function dispatch(string $method, string $route)
    {
        $method = strtoupper($method);
        $route = rtrim($route, '/') ?: '/';

        if (!isset($this->routes[$method][$route])) {
            http_response_code(404);
            throw new Exception('Ruta no encontrada.');
        }

        $action = $this->routes[$method][$route];

        if ($action instanceof Closure) {
            return $action();
        }

        [$controller, $method] = $action;
        $controllerInstance = new $controller();

        return call_user_func([$controllerInstance, $method]);
    }
}
