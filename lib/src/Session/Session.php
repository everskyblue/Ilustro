<?php

namespace Ilustro\Session;

use Ilustro\AppKey;

class Session
{
    /**
     * @param string $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset($_SESSION[AppKey::get()][$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        return $_SESSION[AppKey::get()][$name];
    }

    /**
     * @param string $name
     * @param null|mixed $val
     */
    public static function set($name, $val = null)
    {
        if (!is_array($name) && !is_null($val)) $name = [$name => $val];
        foreach ($name as $key => $value) {
            $_SESSION[AppKey::get()][$key] = $value;
        }
    }

    /**
     * @return mixed
     */
    public static function all()
    {
        return $_SESSION[AppKey::get()];
    }

    /**
     * @param string|array $names
     * @return array|bool
     */
    public static function delete($names)
    {
        $not_del = [];
        if (!is_array($names)) $names = (array)$names;
        foreach ($names as $name) {
            if (!Session::exists($name)) {
                $not_del[] = $name;
            }
            unset($_SESSION[AppKey::get()][$name]);
        }
        return empty($not_del) ? true : $not_del;
    }
}