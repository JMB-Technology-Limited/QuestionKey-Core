<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class TreeVersionStartingNodeRepository extends EntityRepository
{


    public function setAsStartingNode(Node $startingNode) {

        $treeStartingNode = $this->findOneByTreeVersion($startingNode->getTreeVersion());
        if (!$treeStartingNode) {
            $treeStartingNode = New TreeVersionStartingNode;
            $treeStartingNode->setTreeVersion($startingNode->getTreeVersion());
        }
        $treeStartingNode->setNode($startingNode);
        $this->getEntityManager()->persist($treeStartingNode);
        $this->getEntityManager()->flush($treeStartingNode);

    }


}
