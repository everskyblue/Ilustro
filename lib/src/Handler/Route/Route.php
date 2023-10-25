<?php

namespace Ilustro\Handler\Route;

use ReflectionClass;
use ReflectionFunction;

class Route
{
    /**
     * @var string
     */
    const NAMESPACE_CONTROLLER = "App\Controllers\\";

    protected string $rgxParams = "/{(.*?)}/i";

    protected string $upper = "([A-Z]+)?";

    protected string $lower = "([a-z]+)?";

    protected string $number = "([0-9]+)?";

    protected string $string = "([a-zA-Z]+)?";

    protected string $all = "([a-zA-Z0-9_\-\:]+)"; // ([a-zA-Z0-9_\-\:]+)?

    protected array $registerRoutes = [];

    protected array $nameParameters = [];
    
    protected array $middlewares = [];

    /**
     * @var string
     */
    protected $group;

    protected string $name;
    protected string $url;
    protected string $method;
    protected mixed $action;

    /**
     * @var string rgx
     */
    protected $rgxSensitive = "/(string|lower|upper|number)/i";

    /**
     * @var string $nsc
     * @return $this
     */
    public function setNamespaceController($nsc)
    {
        $this->namespace_controller = $nsc;
        return $this;
    }

    public function addName(string $name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function setMiddleware(string $mdw)
    {
        if (!in_array($mdw, $this->middlewares))
            array_push($this->middlewares, $mdw);
        return $this;
    }
    
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @var string $uri
     * @var callable $c
     */
    public function group($uri, callable $c)
    {
        $this->group = $uri;

        call_user_func($c, $this);
        
        return $this;
    }

    /**
     * @var array $via methods permissions
     * @var string|array $url
     * @var string|callable $callback
     * @return $this
     */
    public function map(array $via, $url, $callback)
    {
        $this->registerActions(implode(",", $via), $url, $callback);

        return $this;
    }

    /**
     * @param string $url
     * @param callable|array $callback
     * @return Route
     */
    public function get($url, $callback)
    {
        return $this->registerActions("GET", $url, $callback);
    }

    /**
     * @param string $url
     * @param callable|array $callback
     * @return Route
     */
    public function post($url, $callback)
    {
        return $this->registerActions("POST", $url, $callback);
    }

    /**
     * @param string $url
     * @param callable|array $c
     * @return Route
     */
    public function put($url, $c)
    {
        return $this->registerActions("PUT", $url, $c);
    }

    /**
     * @param string $url
     * @param callable|array $c
     * @return Route
     */
    public function patch($url, $c)
    {
        return $this->registerActions("PATCH", $url, $c);
    }

    /**
     * @param string $url
     * @param callable|array $c
     * @return Route
     */
    public function delete($url, $c)
    {
        return $this->registerActions("DELETE", $url, $c);
    }

    /**
     * @param string $via
     * @param callable|array $url
     * @param callable|string $exec
     * @return $this
     */
    private function registerActions($via, $url, $exec)
    {
        $url = empty($this->group)
            ? $this->resolveParameterUrl($url)
            : $this->group . $this->resolveParameterUrl($url);

        $this->url = $url;
        $this->method = $via;
        $this->action = $exec;

        $this->registerRoutes[] = [
            "method" => $via,
            "url" => $url,
            "action" => $exec,
        ];

        return $this;
    }

    /**
     * @param string $url
     * @return string
     */
    private function resolveParameterUrl($url)
    {
        if (preg_match_all($this->rgxParams, $url, $matches) > 0) {
            foreach ($matches[1] as $index => $params) {
                if (strpos($params, ":") !== false) {
                    list($name, $type) = explode(":", $params, 2);
                    $this->nameParameters[] = $name;
                    if (preg_match($this->rgxSensitive, $type) > 0) {
                        $url = str_replace(
                            $matches[0][$index],
                            $this->{$type},
                            $url
                        );
                    } else {
                        throw new \Exception(
                            "sensitive type no exists {$type}"
                        );
                    }
                } else {
                    $url = str_replace($matches[0][$index], $this->all, $url);
                }
            }
        }
        return preg_replace("/[\/|\=]/", '\\\\$0', $url) . "/?";
    }

    public function getRegisterRoutes()
    {
        return $this->registerRoutes;
    }

    public static function __callStatic(string $name, array $params)
    {
        return call_user_func_array([new Route(), substr($name, 1)], $params);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAction()
    {
        return $this->action;
    }
}
