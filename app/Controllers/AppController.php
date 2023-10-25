<?php

namespace App\Controllers;

use Ilustro\Wrapper\Capsule;

abstract class AppController
{
    protected $container;
    
    /**
     * @param $container
     */
    public function __construct(Capsule $container)
    {
        $this->container = $container;
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->container->{$key};
    }
}