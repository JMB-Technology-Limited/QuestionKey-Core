<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\TreeVersion;


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
        $nodeOptionRepo = $this->doctrine->getRepository('QuestionKeyBundle:NodeOption');

        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion);
        foreach($nodes as $node) {

            $foundIncoming = false;

            if ($nodeOptionRepo->hasActiveIncomingNodeOptionsForNode($node)) {
                $foundIncoming = true;
            }

            if ($treeStartingNode && $treeStartingNode->getNode()->getId() == $node->getId()) {
                $foundIncoming = true;
            }

            if (!$foundIncoming) {
                $this->unreachableNodes[] = $node;
            }
        }

    }

    /**
     * @return array
     */
    public function getUnreachableNodes()
    {
        return $this->unreachableNodes;
    }



}