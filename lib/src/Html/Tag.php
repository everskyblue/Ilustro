<?php

namespace Kowo\Ilustro\Html;

/**
 * @package Kowo\Ilustro\Html
 */
class Tag {

    /**
     * @var const VERSION
     */
    const VERSION = '1.0-beta';

    /**
     * @var const CLOSE
     */
    const CLOSE = true;

    /**
     * @var const NOT_CLOSE
     */
    const NOT_CLOSE = false;

    /**
     * @var TreeElement
     */
    private static $e;

    /**
     * @param TreeElement | null
     * @return InsertChildNode
     */
    public static function structure(TreeElement $he = null)
    {
        return new InsertChildNode($he ?: self::$e);
    }

    /**
     * @param string $t
     * @param string|null $a
     * @return AppendNode
     */
    public static function append($t, $a = nul, TreeElement $to = null)
    {
        return new AppendNode($to ?: self::$e, $t, $a);
    }

    /**
     * @param array $attrs
     * @return AttrNode
     */
    public static function attr(array $attrs)
    {
        return new AttrNode($attrs);
    }

    /**
     * @param string $name
     * @param array $param
     * @return TreeElement
     */
    public static function __callStatic($name, $param)
    {
        return (static::$e=call_user_func([new TreeElement($name), 'addNode'],$name, $param));
    }

    /**
     * @param TreeElement $e
     * @return string
     */
    public static function render($e)
    {
        return self::createHTML($e->getNode());
    }

    /**
     * @param array $nodes
     * @return string
     */
    private static function createHTML(array $nodes)
    {
        $tag = '';
        foreach ($nodes as $node) {
            $tag .= '<'. $node['nodeName'];
            if ($node['attr']) {
                foreach ($node['attr']->get() as $key => $val) {
                    $tag .= ' '. $key . '="' . $val .'"';
                }
            }
            $tag .= '>';
            if (isset($node['text'])){
                $tag .= $node['text'];
            }

            $tag .= self::createHTML($node['body']);

            if (isset($node['close']) && $node['close']){
                $tag .= '</'. $node['nodeName'] .'>';
            }
        }

        return $tag;
    }
}