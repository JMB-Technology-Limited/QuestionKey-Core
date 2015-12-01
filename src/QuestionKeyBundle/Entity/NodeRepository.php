<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\TreeVersion;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeRepository extends EntityRepository
{

    public function findEndingNodesByTreeVersion(TreeVersion $treeVersion)
    {
        return $this->getEntityManager()
            ->createQuery(
            'SELECT n FROM QuestionKeyBundle:Node n '.
            'LEFT JOIN n.nodeOptionsSource nod '.
            'WHERE n.treeVersion = :tree_version AND nod.id IS NULL '.
            'ORDER BY n.title ASC'
            )
            ->setParameter('tree_version', $treeVersion)
            ->getResult();
    }

}
