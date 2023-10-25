<?php

namespace Ilustro\Html;

/**
 * @package Ilustro\Html
 */
abstract class MagicTags {

    use StructNode;

    /**
     * @param string $tag
     * @return $this
     */
    public function __get($tag)
    {
        $this->addNode($tag, []);

        return $this;
    }

    /**
     * @param string $tag
     * @param array $args
     * @return $this
     */
    public function __call($tag, $args)
    {
        $this->addNode($tag, $args);

        return $this;
    }

    /**
     * @param array $node
     * @param mixed $arg
     * @return array
     */
    protected function setKeyValNode($node, $arg)
    {
        if ($arg instanceof AttrNode) {
            $node['attr'] = $arg;
        } elseif (is_array($arg)){
            $node['attr'] = Tag::attr($arg);
        } elseif (is_string($arg)) {
            $node['text'] = $arg;
        } elseif (is_bool($arg)) {
            $node['close'] = $arg;
        }
        return $node;
    }


    /**
     * @param array $node
     * @param mixed $arg
     * @return array
     */
    protected function setOptionNode($node, $args)
    {
        if (is_array($args)) {
            foreach ($args as $arg) {
                $node = $this->setKeyValNode($node, $arg);
            }
        } else {
            $node = $this->setKeyValNode($node, $args);
        }
        return $node;
    }
}