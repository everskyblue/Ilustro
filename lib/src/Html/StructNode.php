<?php

namespace Kowo\Ilustro\Html;

/**
 * @package Kowo\Ilustro\Html
 */
trait StructNode {
    /**
     * @param string $t node name
     * @param AttrNode $a attributes
     * @return array
     */
    protected function node($t, AttrNode $a = null)
    {
        return [
            'nodeName' => $t,
            'attr' => $a,
            'close' => Tag::CLOSE,
            'body' => []
        ];
    }

    /**
     * @param string $tag
     * @return string
     */
    public function normalizeTag($tag)
    {
        return str_replace('_', '-', $tag);
    }
}