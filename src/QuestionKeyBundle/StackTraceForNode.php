<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class StackTraceForNode {

    protected $data;

    protected $cachedPublicId;

    function __construct(Node $node) {
        $this->data = array(
            array('node'=>$node, 'nodeOption'=>null),
        );
    }

    public function getCurrentNode() {
        return $this->data[0]['node'];
    }

    protected function addCurrent(NodeOption $nodeOption, Node $node) {
        array_unshift($this->data, array('node'=>$node, 'nodeOption'=>$nodeOption));
        $this->cachedPublicId = null;
    }

    public function isNodeOptionAlreadyKnown(NodeOption $nodeOption) {
        foreach ($this->data as $data) {
            if ($data['nodeOption'] && $data['nodeOption']->getId() == $nodeOption->getId()) {
                return true;
            }
        }
        return false;
    }

    public function getStackTraceWithNewCurrent(NodeOption $nodeOption, Node $node) {
        $return = clone $this;
        $return->addCurrent($nodeOption, $node);
        return $return;
    }

    public function getData() {
        return $this->data;
    }

    public function getPublicId() {
        if (is_null($this->cachedPublicId)) {
            $text = '';
            foreach($this->data as $data) {
                $text .= '=NODE='.$data['node']->getPublicId();
                if ($data['nodeOption']) {
                    $text .= '=NODEOPTION='.$data['nodeOption']->getPublicId();
                }
            }
            $this->cachedPublicId = md5($text);
        }
        return $this->cachedPublicId;
    }

}
