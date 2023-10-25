<?php

namespace Ilustro\Handler;

use ReflectionFunction;
use ReflectionClass;
use Ilustro\Handler\Route\Route;
use Ilustro\Wrapper\Capsule;

final class Router
{
    private array $routes = [];

    public function __construct(private Capsule $capsule, private array $middlewares = [])
    {
    }

    public function get(string $url, string|callable $handler): Route
    {
        return $this->push("get", $url, $handler);
    }

    public function post(string $url, string|callable $handler): Route
    {
        return $this->push("post", $url, $handler);
    }

    public function put(string $url, string|callable $handler): Route
    {
        return $this->push("put", $url, $handler);
    }

    public function patch(string $url, string|callable $handler): Route
    {
        return $this->push("patch", $url, $handler);
    }

    public function delete(string $url, string|callable $handler): Route
    {
        return $this->push("delete", $url, $handler);
    }

    public function options(
        array $methods,
        string $url,
        string|callable $handler
    ) {
    }

    public function group(string $url, callable $handler): Route
    {
        return $this->push("group", $url, $handler);
    }

    public function push(string $method, string $url, string|callable $handler): Route
    {
        array_push(
            $this->routes,
            $route = Route::{"_" . $method}($url, $handler)
        );
        foreach ($this->middlewares as $mdw) {
            $route->setMiddleware($mdw);
        }
        return $route;
    }

    public function invokeAction(string|callable$handler, array $params): mixed
    {
        if (is_callable($handler)) {
            $args = (new ReflectionFunction($handler))->getParameters();
            $this->setParameters($args, $params);
            return call_user_func_array($handler, $args);
        }

        list($nameclass, $method) = explode("@", $handler, 2);

        $nameclass = Route::NAMESPACE_CONTROLLER . $nameclass;

        $reflection = new \ReflectionClass($nameclass);

        if (!$reflection->isInstantiable()) {
            throw new \LogicException(
                "class not instance {$reflection->getName()}"
            );
        }
        
        $args = $reflection->getMethod($method)->getParameters();
        $this->setParameters($args, $params);

        return call_user_func_array(
            [$reflection->newInstance($this->capsule), $method],
            $args
        );
    }

    protected function setParameters(array &$nameParameters, array &$urlParams)
    {
        foreach ($nameParameters as $i => $parameter) {
            $nameParameters[$i] = $this->capsule->{$parameter->getName()}?:null;
        }
        array_push($nameParameters, ...$urlParams);
    }

    public function getRegisterRoutes(): array
    {
        return $this->routes;
    }
}
