<?php

namespace Kowo\Ilustro;

use Kowo\Ilustro\Wrapper\Capsule as Container;
use Kowo\Ilustro\Handler\Route\Route;
use Kowo\Ilustro\Handler\Route\Dispatcher;
use Kowo\Ilustro\Http\{Request, Response};

/**
 * main class
 * @property Route $route
 * @property Request $request
 * @property Response $response
 * @property Dispacther $dispacther
 */

class Ilustro {
    /**
     * @var Container
     */
    public $container;

    /**
     * @param mixed $firware
     */
    public function __construct($firware)
    {
        $this->container = new Container();
        $this->container->register('firware', function () use ($firware) {
            return $firware;
        });
    }

    /**
     * registrar clases globales del framework
     * @return void
     */
    protected function initialContainer()
    {
        $this->container->register('route', Route::class);
        $this->container->register('request', Request::class);
        $this->container->register('response', Response::class, ['request']);
        $this->container->register('dispatcher', Dispatcher::class, ['capsule']);
    }

    /**
     * incluye las rutas
     * @return void
     */
    protected function web()
    {
        $route = function($route){
            $f = \config('web');
            foreach ($f as $file) {
                include $file;
            }
        };
        $route($this);
    }

    /**
     * enviar y ejecutar la aplicacion
     * @return void
     */
    public function send()
    {
        $this->dispatcher->send(function () {
            return file_get_contents(ILUSTRO_BASE . '/public/404.html');
        });
    }

    /**
     * instaciar la clase
     * @return self
     */
    public static function create($firware = null)
    {
        return new self($firware);
    }

    /**
     * configurar el framework
     * @return void
     */
    public function dispatchConfigApplication()
    {
        ini_set('date.timezone', \config('app.timezone'));
        switch (\config('app.env')) {
            case 'production':
                ini_set('display_errors', true);
            break;
            default:
                ini_set('display_errors', false);
            break;
        }
    }

    /**
     * @return void
     */
    public function dispatchAppliction()
    {
        $this->initialContainer();
        $this->web();
        $this->send();
    }

    /**
     * @description
     * llamar funcion anonima para tipo de peticion http
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->route, $name], $args);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->container[$key]) ? $this->container[$key] : null;
    }
}