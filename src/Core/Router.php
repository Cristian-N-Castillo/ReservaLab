<?php

declare(strict_types=1);

namespace Core;

use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

final class Router
{
    /**
     * @var array<string, array<string, array{
     *      action: array{0: class-string, 1: string},
     *      middleware: array<class-string>
     * }>>
     */
    private array $routes = [];

    public function get(string $uri, array $action, array $middleware = []): void
    {
        $this->routes['GET'][$uri] = [
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function post(string $uri, array $action, array $middleware = []): void
    {
        $this->routes['POST'][$uri] = [
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): mixed
    {
        $method = $request->method();
        $uri = $request->uri();

        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routeUri => $route) {

            $pattern = preg_replace(
                '/\{[^\/]+\}/',
                '([^/]+)',
                $routeUri
            );

            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            array_shift($matches);

            foreach ($route['middleware'] as $middlewareClass) {

                if (!class_exists($middlewareClass)) {
                    throw new RuntimeException(
                        "El middleware '{$middlewareClass}' no existe."
                    );
                }

                $middleware = new $middlewareClass();

                if (!$middleware instanceof MiddlewareInterface) {
                    throw new RuntimeException(
                        "{$middlewareClass} debe implementar Core\\MiddlewareInterface."
                    );
                }

                $middleware->handle($request);
            }

            [$controller, $action] = $route['action'];

            if (!class_exists($controller)) {
                throw new RuntimeException(
                    "El controlador '{$controller}' no existe."
                );
            }

            $instance = new $controller();

            if (!method_exists($instance, $action)) {
                throw new RuntimeException(
                    "El método '{$action}' no existe en {$controller}."
                );
            }

            $reflection = new ReflectionMethod($instance, $action);

            $arguments = [];
            $routeIndex = 0;

            foreach ($reflection->getParameters() as $parameter) {

                $type = $parameter->getType();

                if (
                    $type instanceof ReflectionNamedType &&
                    $type->getName() === Request::class
                ) {
                    $arguments[] = $request;
                    continue;
                }

                $arguments[] = $matches[$routeIndex] ?? null;
                $routeIndex++;
            }

            return $reflection->invokeArgs($instance, $arguments);
        }

        return null;
    }
}