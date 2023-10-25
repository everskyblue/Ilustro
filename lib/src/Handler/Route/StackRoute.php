<?php

namespace Ilustro\Handler\Route;

use Ilustro\Ilustro;
use Ilustro\Handler\Route;

class StackRoute
{
    /**
     * @var Routes
     */
    protected static $route;

    /**
     * @var array
     */
    protected static $optionsRoute = [];

    /**
     * @return Route
     */
    public static function getRoute()
    {
        if (self::$route == null && !class_exists('Ilustro\Ilustro')) {
            self::$route = new Route();
        }elseif (is_null(self::$route)){
            self::$route = Ilustro::getInstance();
        }
        return self::$route;
    }

    /**
     * @return bool|mixed
     */
    public static function getUrl()
    {
        return static::getOpt('url');
    }

    /**
     * @return bool|mixed
     */
    public static function mdw()
    {
        return static::getOpt('mwd');
    }

    /**
     * @return bool|mixed
     */
    public static function asRoute()
    {
        return static::getOpt('as');
    }

    /**
     * @param string $k
     * @return bool|mixed
     */
    public static function getOpt($k)
    {
        return isset(self::$optionsRoute[$k]) ? self::$optionsRoute[$k] : false;
    }

    /**
     * @param array $methods
     * @param string|array $u
     * @param callable|array $ac
     */
    public static function map(array $methods, $u, $ac)
    {
        $route = self::getRoute();

        if (is_array($u)) {
             $u = $u['url'];
            self::setOptionsRoute($u);
        }

        $route->map($methods, $u, $ac);
    }

    /**
     * @param string $name method of route
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($name, $params)
    {
        $route = self::getRoute();
        $things = $url = array_shift($params);
        $action = array_shift($params);

        if (is_array($things)) {
            $url = $things['url'];
            static::setOptionsRoute($things);
        }

        return $route->{$name}($url, $action);
    }

    /**
     * @param array $optionsRoute
     */
    public static function setOptionsRoute(array $optionsRoute)
    {
        foreach ($optionsRoute as $key => $value) {
            if (!isset(self::$optionsRoute[$key])) {
                self::$optionsRoute[$key] = [];
            }

            switch ($key) {

                case 'mdw':
                    array_push(self::$optionsRoute[$key], $value);
                    break;

                case 'as':
                    array_push(self::$optionsRoute[$key], $value);
                    break;
            }
        }
    }
}