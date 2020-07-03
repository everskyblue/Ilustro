<?php

namespace App\Controllers;


abstract class AppController
{
    protected $container;
    
    /**
     * @param \Kowo\Ilustro\Wrapper\Capsule $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->container->{$key};
    }
}