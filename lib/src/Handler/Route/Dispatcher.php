<?php

namespace Ilustro\Handler\Route;

use config;
use Exception;
use Ilustro\Http\Url\Component as ComponentUrl;
use Ilustro\Wrapper\Capsule;
use Ilustro\Http\{Response, Request};
use Ilustro\Handler\Router;

class Dispatcher
{
    protected Router $route;

    protected Request $request;

    protected Response $response;

    protected ComponentUrl $url;

    protected Capsule $container;

    public function __construct(Capsule $container)
    {
        $this->route =
            is_string($container->route) &&
            $container->route == "Ilustro\Handler\Route\StackRoute"
                ? forward_static_call($container->route . "::getRoute")
                : $container->route;
        $this->container = $container;
        $this->request = $container->request;
        $this->response = $container->response;
        $this->url = new ComponentUrl($container->request->getPath());
    }
    
    protected function revision(array $mdws): mixed
    {
        if (empty($mdws)) return true;
        $currentMiddleware = array_shift($mdws);
        if (!class_exists($currentMiddleware)) throw new Exception("no es una clase");;
        return $currentMiddleware::handle($this->request, function (bool $passed) use($mdws) {
            if (!$passed){
                throw new Exception("middleware error");
            } else {
                $this->revision($mdws);
            }
            return true;
        });
    }

    /**
     * @throws \Exception
     */
    public function send(callable $f = null)
    {
        foreach ($this->route->getRegisterRoutes() as $route) {
            if ($m = $this->matchUrl($route->getUrl())) {
                array_shift($m);
                if (!$this->request->compareMethod($route->getMethod())) {
                    throw new \Exception(
                        sprintf(
                            "method %s not issued by this url",
                            $route->getMethod()
                        )
                    );
                    
                }
                $this->revision($route->getMiddlewares());
                $this->actionController($route->getAction(), $m);
                break;
            }
        }

        if (!$this->response->isSuccess()) {
            if (
                config("app.fs_route") &&
                ($file = FileSystemRoute::match(
                    config("app.path_pages"),
                    $this->url->getPath()
                ))
            ) {
                $handler = require_once $file;
                $this->actionController($handler);
            } else {
                $this->actionController($f, [], 404);
            }
        }

        $this->response->send();
    }

    private function actionController(
        mixed $handler,
        array $params = [],
        int $status = 200
    ) {
        $this->response->withStatus($status)->withContent(
            $this->route->invokeAction($handler, $params)
        );
    }

    public function matchUrl(string $url): bool | array
    {
        return preg_match("@^" . $url . '$@', $this->url->getPath(), $m) === 0
            ? false
            : $m;
    }
}
