<?php

namespace Kowo\Ilustro\Handler\Bug;


class MistakeConfig {

    /**
     * @var array
     */
    private $app = [
        'debug' => true,

        'log' => false,

        'non_report_error_type' => null
    ];

    /**
     * @var array
     */
    private $handler = [
    ];

    /**
     * @param array $c
     */
    public function __construct(array $c = [])
    {
        $this->app = array_merge($this->app, $c);
    }

    /**
     * @param array $h
     */
    public function setHandler(array $h)
    {
        $this->handler[] += $h;
    }

    /**
     * @return mixed
     */
    public function __get($k)
    {
        return $this->app[$k];
    }
}