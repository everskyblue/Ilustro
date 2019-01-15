<?php

namespace Kowo\Ilustro\Handler\Route;


class Route {

    /**
     * @var string
     */
    protected $namespace_controller = 'App\Controllers\\';

    /**
     * @string rgxParams
     */
    protected $rgxParams = '/{(.*?)}/i';

    /**
     * @var string regex
     */
    protected $upper = '([A-Z]+)?';

    /**
     * @var string regex
     */
    protected $lower = '([a-z]+)?';

    /**
     * @var string regex
     */
    protected $number = '([0-9]+)?';

    /**
     * @var string regex
     */
    protected $string = '([a-zA-Z]+)?';

    /**
     * @var string regex
     */
    protected $all = '([a-zA-Z0-9_\-\:]+)'; // ([a-zA-Z0-9_\-\:]+)?

    /**
     * @var array
     */
    protected $registerRoutes = [];

    /**
     * @var array
     */
    protected $nameParameters = [];

    /**
     * @var string
     */
    protected $group = '';

    /**
     * @var string rgx
     */
    protected $rgxSensitive = '/(string|lower|upper|number)/i';

    /**
     * @var string $nsc
     * @return $this
     */
    public function setNamespaceController($nsc)
    {
        $this->namespace_controller = $nsc;
        return $this;
    }

    /**
     * @var string $uri
     * @var callable $c
     */
    public function group($uri, callable $c)
    {
        $this->group = $uri;

        call_user_func($c, $this);
    }

    /**
     * @var array $via methods permissions
     * @var string|array $url
     * @var string|callable $callback
     * @return $this
     */
    public function map(array $via, $url, $callback)
    {
        $this->registerActions(implode(',', $via), $url, $callback);

        return $this;
    }

    /**
     * @param string $url
     * @param callable|array $callback
     * @return Route
     */
    public function get($url, $callback)
    {
        return $this->registerActions('GET', $url, $callback);
    }

    /**
     * @param string $url
     * @param callable|array $callback
     * @return Route
     */
    public function post($url, $callback)
    {
        return $this->registerActions('POST', $url, $callback);
    }

    /**
     * @param string $url
     * @param callable|array $c
     * @return Route
     */
    public function put($url, $c)
    {
        return $this->registerActions('PUT', $url, $c);
    }

    /**
     * @param string $url
     * @param callable|array $c
     * @return Route
     */
    public function patch($url, $c)
    {
        return $this->registerActions('PATCH', $url, $c);
    }

    /**
     * @param string $url
     * @param callable|array $c
     * @return Route
     */
    public function delete($url, $c)
    {
        return $this->registerActions('DELETE', $url, $c);
    }

    /**
     * @param string $via
     * @param callable|array $url
     * @param callable|string $exec
     * @return $this
     */
    private function registerActions($via, $url, $exec)
    {
        $url = empty($this->group) ? $this->resolveParameterUrl($url) : $this->group . $this->resolveParameterUrl($url);

        $this->registerRoutes[] = [
            'method' => $via,
            'url'    => $url,
            'action' => $exec
        ];

        return $this;
    }

    /**
     * @param string $url
     * @return string
     */
    private function resolveParameterUrl($url)
    {
        if(preg_match_all($this->rgxParams, $url, $matches) > 0) {
            foreach ($matches[1] as $index => $params) {
                if (strpos($params, ':') !== false) {
                    list($name, $type) = explode(':', $params, 2);
                    $this->nameParameters[] = $name;
                    if (preg_match($this->rgxSensitive, $type) > 0) {
                        $url = str_replace($matches[0][$index], $this->{$type}, $url);
                    }else {
                        throw new \Exception("sensitive type no exists {$type}");
                    }
                }else {
                    $url = str_replace($matches[0][$index], $this->all, $url);
                }
            }
        }
        return preg_replace('/[\/|\=]/', '\\\\$0', $url).'/?';
    }

    /**
     * @return array
     */
    public function getRegisterRoutes()
    {
        return $this->registerRoutes;
    }

    /**
     * @param string $str
     * @param $container
     * @return array
     */
    public function invokeAction($str, $params, $container = null)
    {
        if (is_callable($str)) {
            array_push($params, $container);
            return call_user_func_array($str, $params);
        }

        list($nameclass, $method) = explode('@', $str, 2);

        $nameclass = $this->namespace_controller . $nameclass;
        
        $reflection = new \ReflectionClass($nameclass);

        if(!$reflection->isInstantiable()){
            throw new \LogicException("class not instance {$reflection->getName()}");
        }

        return call_user_func_array([$reflection->newInstance($container), $method], $params);
    }
}