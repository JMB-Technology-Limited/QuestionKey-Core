<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class GetUnreachableBitsOfTree
{


    protected $doctrine;
    protected $treeVersion;

    protected $startNode;

    protected $unreachableNodes = array();

    function __construct($doctrine, TreeVersion $treeVersion) {
        $this->doctrine = $doctrine;
        $this->treeVersion = $treeVersion;
    }

    public function go() {

        $this->unreachableNodes = array();

        $treeStartingNodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);

        $nodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:Node');

        // The sort is here so we get consistent results
        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion, array('id'=>'ASC'));
        foreach($nodes as $node) {

            if ($treeStartingNode == null || !$this->isNodeReachable($node, $treeStartingNode)) {
                $this->unreachableNodes[] = $node;
            }
        }

    }

    private function isNodeReachable(Node $node, TreeVersionStartingNode $treeVersionStartingNode) {

        if ($treeVersionStartingNode && $treeVersionStartingNode->getNode()->getId() == $node->getId()) {
            return true;
        }

        $nodeOptionRepo = $this->doctrine->getRepository('QuestionKeyBundle:NodeOption');
        foreach($nodeOptionRepo->findActiveIncomingNodeOptionsForNode($node) as $nodeOption) {
            if ($this->isNodeReachable($nodeOption->getNode(), $treeVersionStartingNode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getUnreachableNodes()
    {
        return $this->unreachableNodes;
    }



}