<?php

namespace QuestionKeyBundle;


use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class CopyNewVersionOfTree
{

    protected $doctrine;
    protected $oldVersion;
    protected $newVersion;

    function __construct($doctrine, TreeVersion $oldVersion, TreeVersion $newVersion) {
        $this->doctrine = $doctrine;
        $this->oldVersion = $oldVersion;
        $this->newVersion = $newVersion;
    }

    public function go() {

        // Tree Option
        $this->newVersion->setFromOldVersion($this->oldVersion);

        // As we call flush in this function, make sure new version will definetly be part of that.
        $this->doctrine->persist($this->newVersion);

        // Nodes
        $nodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:Node');
        $nodes = $nodeRepo->findByTreeVersion($this->oldVersion);
        $newNodes = array();
        foreach ($nodes as $node) {
            $newNodes[$node->getId()] = new Node();
            $newNodes[$node->getId()]->setTreeVersion($this->newVersion);
            $newNodes[$node->getId()]->setTitle($node->getTitle());
            $newNodes[$node->getId()]->setBodyText($node->getBodyText());
            $newNodes[$node->getId()]->setBodyHTML($node->getBodyHTML());
            $newNodes[$node->getId()]->setPublicId($node->getPublicId());
            $newNodes[$node->getId()]->setFromOldVersion($node);
            $this->doctrine->persist($newNodes[$node->getId()]);
        }

        // Node Options
        $nodeOptionRepo = $this->doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $nodeOptions = $nodeOptionRepo->findAllNodeOptionsForTreeVersion($this->oldVersion);
        foreach($nodeOptions as $nodeOption) {
            $newNodeOption = new NodeOption();
            $newNodeOption->setTreeVersion($this->newVersion);
            $newNodeOption->setNode($newNodes[$nodeOption->getNode()->getId()]);
            $newNodeOption->setDestinationNode($newNodes[$nodeOption->getDestinationNode()->getId()]);
            $newNodeOption->setTitle($nodeOption->getTitle());
            $newNodeOption->setBodyHTML($nodeOption->getBodyHTML());
            $newNodeOption->setBodyText($nodeOption->getBodyText());
            $newNodeOption->setSort($nodeOption->getSort());
            $newNodeOption->setPublicId($nodeOption->getPublicId());
            $newNodeOption->setFromOldVersion($nodeOption);
            $this->doctrine->persist($newNodeOption);
        }


        // Tree Start
        $treeStartingNodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->oldVersion);

        if ($treeStartingNode) {
            // we have to flush at this point otherwise when we try and persist at the next stage you get an error.
            $this->doctrine->flush();

            $newTreeVersionStartingNode = new TreeVersionStartingNode();
            $newTreeVersionStartingNode->setTreeVersion($this->newVersion);
            $newTreeVersionStartingNode->setNode($newNodes[$treeStartingNode->getNode()->getId()]);
            $this->doctrine->persist($newTreeVersionStartingNode);
        }

        // final flush
        $this->doctrine->flush();
    }

}
