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
    public function __construct(TreeElement $he, $t, $a)
    {
        $this->setTreeElement($he);
        $this->query($t, $a);
    }

    /**
     * @param string $t
     * @param string $a
     * @return $this
     */
    public function query($t, $a)
    {
        $this->ftag = explode(':', $t);
        $this->fattr = explode(':', $a);
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
     */
    public function push($new_node)
    {
        $this->he->replaceAllNode($this->findPush($this->he->getNode(), $new_node));
    }

    /**
     * @param array $nodes
     * @param string $push
     * @return array
     */
    private function findPush(array $nodes, $push)
    {
        foreach($nodes as $i => $node) {//var_dump('<br><br>',$node,'-----------------');
            $name = $node[$this->type_select[$this->ftag[0]]];
            if ($name === $this->ftag[1]) {
                $si = $this->fattr;
                $na = $node['attr'];
                if(is_object($na) && array_key_exists($si[0],$na->get()) && (strpos($na->get()[$si[0]], $si[1])!==false)) {
                    $node['body'] = array_merge($node['body'], $push);
                }
            }
            $nodes[$i]['body'] = $this->findPush($node['body'], $push);
        }
        return $nodes;
    }
}