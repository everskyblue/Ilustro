<?php

namespace Kowo\Ilustro\Html;


/**
 * @package Kowo\Ilustro\Html
 */
final class AttrNode {
    /**
     * @var array
     */
    private $attr = [];

    /**
     * constructor AttrNode
     * @param array $attr
     */
    public function __construct($attr)
    {
        foreach($attr as $key => $val) {
            $this->attr[$key] = $val;
        }
    }

    /**
     * @param string $key
     * @param string $val
     */
    public function set($key, $val)
    {
        $this->attr[$key] = $val;
    }

    /**
     * @param string $k
     * @param string $v
     * @return bool
     */
    public function compare($k, $v)
    {
        return isset($this->attr[$k]) && in_array($v, explode(' ', $this->attr[$k]));
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->attr;
    }
}