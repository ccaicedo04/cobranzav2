<?php

namespace Core;

use Closure;
use Exception;

class Router
{
    /** @var array */
    private $routes = [];

    /**
     * @param array|Closure $action
     * @return void
     */
    public function get(string $route, $action)
    {
        $this->addRoute('GET', $route, $action);
    }

    /**
     * @param array|Closure $action
     * @return void
     */
    public function post(string $route, $action)
    {
        $this->addRoute('POST', $route, $action);
    }

    /**
     * @param array|Closure $action
     * @return void
     */
    public function match(array $methods, string $route, $action)
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $route, $action);
        }
    }

    /**
     * @param array|Closure $action
     * @return void
     */
    private function addRoute(string $method, string $route, $action)
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
