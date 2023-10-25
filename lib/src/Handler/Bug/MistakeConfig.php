<?php

namespace Ilustro\Handler\Bug;


class MistakeConfig {

    /**
     * @var array
     */
    private $config = [
        'debug' => true,

        'log' => false,

        'exclude_type_errors' => []
    ];

    /**
     * @param array $c
     */
    public function __construct(array $c = [])
    {
        $this->config = array_merge($this->config, $c);
    }

    /**
     * @return mixed
     */
    public function __get($k)
    {
        return $this->config[$k];
    }
}