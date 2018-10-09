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
     * Dispatcher constructor.
     * @param string | Kowo\Ilustro\Handler\Route $route
     * @param Kowo\Ilustro\Http\Request $request
     * @param Kowo\Ilustro\Http\Response $response
     */
    public function __construct($route, Request $request, Response $response)
    {
        $this->route = (is_string($route)&&$route == 'Kowo\Ilustro\Handler\Route\StackRoute')
            ? forward_static_call($route . '::getRoute')
            : $route;
        $this->request = $request;
        $this->response = $response;
        $this->url = new ComponentUrl($request->getPath());
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
            $method = $arr['method'];
            $url    = $arr['url'];
            $action = $arr['action'];

            if ($m = $this->matchUrl($url)) {
                if ($this->request->compareMethod($method)) {
                    $this->response->setDispatcher($this->route, $action, $m, 200);
                } else {
                    throw new \Exception(sprintf('method %s not issued by this url', $method));
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