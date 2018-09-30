<?php

namespace Kowo\Ilustro\Html;

/**
 * @package Kowo\Ilustro\Html
 */
class TreeElement extends MagicTags implements CallerInterface {

    /**
     * @var array
     */
    private $node = [];

    /**
     * @var string
     */
    protected $node_name;

    /**
     * @var array
     */
    protected $insert_node = [];

    /**
     * @param string $name
     * @param array $args
     * @return $this
     */
    public function addNode($name, $args)
    {
        $this->node_name = $this->normalizeTag($name);

        if (count($this->insert_node)) {
            array_push($this->node, $this->insert_node);
        }

        $this->node[] = $this->setOptionNode($this->node($this->node_name), $args);

        return $this;
    }

    /**
     * @param int $g
     * @param array | AttrNode $tgs
     * @param null | AttrNode $attr
     * @return $this
     */
    public function generate($g, $tgs = [], $attr = null)
    {
        if (is_null($attr) && is_object($tgs)) {
            $attr = $tgs;
            $tgs = $this->node_name;
        }

        for ($x = 0; $x < $g; $x++) {
            $nn = $this->utilGen($x, $tgs);
            if (!is_null($attr) && $attr instanceof AttrNode) {
                foreach ($attr->get() as $key => $val) {
                    if (is_array($val)) {
                        if (is_object($nn['attr'])) {
                            $nn['attr']->set($key, $val[$x]);
                        } else{
                            $nn['attr'] = Tag::attr([
                                $key => $val[$x]
                            ]);
                        }
                    } else {
                        $nn['attr'] = $attr;
                    }
                }
            }
            $this->insert_node[] = $nn;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getNodeGen()
    {
        return $this->insert_node;
    }

    /**
     * @return $this
     */
    public function resetNodeGen()
    {
        $this->insert_node = [];
        return $this;
    }

    /**
     * return $this
     */
    public function insideAll()
    {
        $this->insertChild($this->insert_node);
        $this->insert_node = [];
        return $this;
    }

    /**
     * @param array | TreeElement $child
     * @return $this
     */
    public function insertChild($child)
    {
        $this->node[count($this->node) - 1]['body'] = array_merge(
            $this->node[count($this->node) - 1]['body'],
            ($child instanceof TreeElement ? $child->getNode() : $child)
        );
        return $this;
    }

    /**
     * @return $this
     */
    public function insert()
    {
        $this->node = array_merge($this->node, $this->insert_node);
        $this->insert_node = [];
        return $this;
    }

    /**
     * @param array $nn
     * @return $this
     */
    public function replaceAllNode(array $nn)
    {
        $this->node = $nn;

        return $this;
    }

    /**
     * @return array
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param int $i
     * @param array | string $t
     * @return array
     */
    private function utilGen($i, $t)
    {
        if (is_array($t) && isset($t[$i])) {
            $item = $t[$i];
            if (is_array($item)) {
                $tag = $this->setOptionNode($this->node($item[0]), $item[1]);
            } else {
                $tag = $this->node($item);
            }
            return $tag;
        }
        return $this->node($t);
    }
}