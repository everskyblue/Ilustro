<?php

namespace Kowo\Ilustro\Html;



/**
 * @package Html
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
     * @return array
     */
    public function get()
    {
        return $this->attr;
    }
}