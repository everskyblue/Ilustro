<?php

namespace Ilustro\Wrapper;


class Capsule implements \ArrayAccess, \Countable {

    /**
     * @var array
     */
    private $register = array();

    /**
     * @param string $key
     * @param string $val
     * @param array $params
     */
    public function register($key, $val, $params = array())
    {
        $c = function ($c) use ($val, $params) {
            static $s;

            if($s == null) {

                if (!is_callable($val)) {
                    $val = function($c) use ($val, $params) {
                        return class_exists($val) ? $this->getInstance($val,$params) : $val;
                    };
                }

                $s = $val($c);
            }

            return $s;
        };

        $this->register[$key] = $c;
    }

    /**
     * @param string $key
     * @param string|array $value
     */
    public function __set($key, $value)
    {
        $this->register($key, $value);
    }

    /**
     * @param string $name
     * @param array $args
     */
    public function __call($name, $args)
    {
        return \call_user_func_array($this->get($name), $args);
    }

    /**
     * @param string $key
     * @throws \RuntimeException
     * @return mixed
     */
    public function __get($key)
    {
        if(!$this->exists($key)){
            throw new \RuntimeException("the key {$key} not exists");
        }
        return $this->get($key);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->exists($offset);
    }

    /**
     * @param string $offset
     * @throws \RuntimeException
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        if(!$this->exists($offset)){
            throw new \RuntimeException("the key {$offset} not exists");
        }

        return $this->get($offset);
    }

    /**
     * @param string $key
     * @return object
     * @throws \Exception
     */
    protected function get($key)
    {
        if ($this->exists($key)) {
            return $this->invoke($key);
        }
        throw new \Exception("error find key {$ket}");
    }

    protected function invoke($key)
    {
        $o = $this->register[$key];

        if (!is_object($o) && is_callable($o)) {
            $o = $o($this);
        }

        return method_exists($o, '__invoke') ? $o($this) : $o;
    }

    /**
     * @param string $cl
     * @param array $params
     * @return object
     */
    protected function getInstance($cl, $params)
    {
        $reflection = new \ReflectionClass($cl);

        foreach ($params as $i  => $k) {
            $params[$i] = $k == 'capsule' ? $this : $this->$k;
        }

        return $reflection->newInstanceArgs($params);
    }

    /**
     * @param string $offset
     * @param string|array $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->register($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
    }

    /**
     * @return array
     */
    public function count(): int
    {
        return $this->register;
    }

    /**
     * @access protected
     * @param string $key
     */
    protected function delete($key)
    {
        unset($this->register[$key]);
    }

    /**
     * @access protected
     * @param string $key
     * @return bool
     */
    protected function exists(mixed $key): bool
    {
        return isset($this->register[$key]);
    }
}