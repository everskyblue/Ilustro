<?php

namespace Kowo\Ilustro\Html;


/**
 * @package Kowo\Ilustro\Html
 */
class InsertChildNode extends MagicTags implements CallerInterface {

    /**
     * @var TreeElement
     */
    protected $he;

    /**
     * @var AppendNode
     */
    protected $append_node;

    /**
     * @var array
     */
    protected $insert = [];

    /**
     * @param TreeElement $he
     */
    public function __construct(TreeElement $he)
    {
        $this->he = $he;
    }

    /**
     * @param string $node
     * @param array
     */
    public function addNode($node, $params)
    {
        $this->insert[] = $this->setOptionNode($this->node($node), $params);
    }

    /**
     * @param array $m
     * @param array $t
     * @return array
     */
    protected function recursive(array $m, array $t)
    {
        if(isset($t[0])&&empty($t[0]['body'])) {
            array_push($m['body'], $this->recursive(array_shift($t), $t));
        }
        return $m;
    }

    /**
     * @param string $t
     * @param string $a
     */
    public function append($t, $a)
    {
        Tag::append($t, $a)->push(
            $this->recursive(array_shift($this->insert), $this->insert)
        );
    }
}