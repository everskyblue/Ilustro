<?php

namespace Ilustro\Html;


/**
 * @package Ilustro\Html
 */
class InsertChildNode extends MagicTags implements CallerInterface {

    /**
     * @var TreeElement
     */
    protected $treeElement;

    /**
     * @var AppendNode
     */
    protected $append_node;

    /**
     * @var array
     */
    protected $insert = [];

    /**
     * @param TreeElement $treeElement
     */
    public function __construct(TreeElement $treeElement)
    {
        $this->treeElement = $treeElement;
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
    protected function recursive(array $parent, array $childs)
    {
        if(isset($childs[0])&&empty($childs[0]['body'])) {
            array_push($parent['body'], $this->recursive(array_shift($childs), $childs));
        }
        return $parent;
    }

    /**
     * @param string $t
     * @param string|null $a
     * @return AppendNode
     */
    public function append($t, $a = null)
    {
        return Tag::append($t, $a, $this->treeElement)->push(
            [$this->recursive(array_shift($this->insert), $this->insert)]
        );
    }
}