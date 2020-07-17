<?php

namespace Kowo\Ilustro\Html;


/**
 * @package Kowo\Ilustro\Html
 */
class AppendNode {

    /**
     * @var array
     */
    protected $type_select = [
        'tag'   => 'nodeName',
        'attr'  => 'attr'
    ];

    /**
     * @var string
     */
    protected $ftag;

    /**
     * @var string
     */
    protected $fattr;

    /**
     * @var TreeElement
     */
    protected $he;

    /**
     * @param TreeElement $he
     * @param string $t
     * @param string $a
     */
    public function __construct(TreeElement $he, $t, $a = false)
    {
        $this->setTreeElement($he);
        $this->query($t, $a);
    }

    /**
     * @param string $t
     * @param string $a
     * @return $this
     */
    public function query($t, $a = false)
    {
        $this->ftag = explode(':', $t);
        $this->fattr = $a ? explode(':', $a) : $a;
        return $this;
    }

    /**
     * @param TreeElement $te
     * @return $this
     */
    public function setTreeElement(TreeElement $te)
    {
        $this->he = $te;
        return $this;
    }

    /**
     * @param array $new_node
     * @return $this
     */
    public function push($new_node)
    {
        $this->he->replaceAllNode($this->findPush($this->he->getNode(), $new_node));
        return $this;
    }

    /**
     * @param array $nodes
     * @param string $push
     * @return array
     */
    private function findPush(array $nodes, $push)
    {
        foreach($nodes as $i => $node) {
            $type = $this->ftag[0];
            $name = $node[$this->type_select[$type]];
            if ($type === 'tag' && $name === $this->ftag[1]) {
                $node = $this->mergeNode($node, $push);
            } elseif ($type === 'attr' && isset($node['attr']) && call_user_func_array([$node['attr'], 'compare'], explode('@', $this->ftag[1]))) {
                $node = $this->mergeNode($node, $push);
            }
        
            $nodes[$i]['body'] = $this->findPush($node['body'], $push);
        }
        return $nodes;
    }

    /**
     * @param array $node
     * @param array $push
     * @return array
     */
    private function mergeNode($node, $push)
    {
        if (is_array($this->fattr)) {
            $si = $this->fattr;
            $na = $node['attr'];
            if(is_object($na) && array_key_exists($si[0],$na->get()) && (strpos($na->get()[$si[0]], $si[1])!==false)) {
                $node['body'] = array_merge($node['body'], $push);
            }
        } else {
            $node['body'] = array_merge($node['body'], $push);
        }
        return $node;
    }
}