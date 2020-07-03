<?php

namespace Kowo\Ilustro;

use Kowo\Ilustro\Handler\Bug\Mistake;
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

    protected $handler_error;

    /**
     * @param mixed $firware
     */
    public function __construct(Mistake $ms)
    {
        $this->handler_error = $ms;
        $this->container = new Container();
        $this->initialContainer();
    }

    /**
     * registrar clases globales del framework
     * @return void
     */
    protected function initialContainer()
    {
        $this->container->register('route', Route::class);
        $this->container->register('request', Request::class);
        $this->container->register('response', function($container) {
            return new Response($container->request, $container);
        });
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
     * @param Mistake $ms
     * @return self
     */
    public static function create(Mistake $ms)
    {

        return new self($ms);
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
                ini_set('display_errors', 0);
            break;
            case 'development':
            default:
                ini_set('display_errors', 1);
            break;
        }
        $this->handler_error->setHandler()->initializeContent();
    }

    /**
     * @return void
     */
    public function dispatchAppliction()
    {
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