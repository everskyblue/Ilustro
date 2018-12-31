<?php

namespace Kowo\Ilustro\Handler\Route;


use Kowo\Ilustro\Http\Request;
use Kowo\Ilustro\Http\Response;
use Kowo\Ilustro\Http\Url\Component as ComponentUrl;


class Dispatcher
{
    /**
     * @var Kowo\Ilustro\Handler\Route
     */
    protected $route;

    /**
     * @var Kowo\Ilustro\Http\Request
     */
    protected $request;

    /**
     * @var Kowo\Ilustro\Http\Response
     */
    protected $response;

    /**
     * @var ComponentUrl
     */
    protected $url;
    
    /**
     * @var Kowo\Ilustro\Wrapper\Capsule
     */
    protected $container;

    /**
     * Dispatcher constructor.
     * @param string | Kowo\Ilustro\Handler\Route $route
     * @param Kowo\Ilustro\Http\Request $request
     * @param Kowo\Ilustro\Http\Response $response
     */
    public function __construct($container)
    {
        $this->route = (is_string($container->route)&&$container->route == 'Kowo\Ilustro\Handler\Route\StackRoute')
            ? forward_static_call($container->route . '::getRoute')
            : $container->route;
        $this->container = $container;
        $this->request = $container->request;
        $this->response = $container->response;
        $this->url = new ComponentUrl($container->request->getPath());
    }

    protected function resolveAction($c)
    {
        return (is_string($c)
                 ? $this->route->resolveClass($c, $this->container)
                 : $c
        );
    }

    /**
     * @param callable|null $f
     * @throws \Exception
     */
    public function send(callable $f = null)
    {
        foreach ($this->route->getRegisterRoutes() as $index => $arr) {
            if ($m = $this->matchUrl($arr['url'])) {
                $mu = array_shift($m);
                if ($this->request->compareMethod($arr['method'])) {
                    $this->response->setDispatcher($this->route, $arr['action'], $m, 200);
                } else {
                    throw new \Exception(sprintf('method %s not issued by this url', $arr['method']));
                }
                break;
            }
        }

        if (!$this->response->isSuccess()) {
            $this->response->setDispatcher($this->route, ($f?:function(){})->bind($f, $this), [], 404);
        }

        $this->response->send();
    }

    /**
     * @param string $url
     * @return bool|array
     */
    public function matchUrl($url)
    {
        return preg_match('@^' . $url . '$@', $this->url->getPath(), $m) === 0 ? false : $m;
    }
}