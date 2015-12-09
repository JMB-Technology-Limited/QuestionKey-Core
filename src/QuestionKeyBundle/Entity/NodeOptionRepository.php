<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\TreeVersion;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeOptionRepository extends EntityRepository
{


    public function findActiveNodeOptionsForNode(Node $node)
    {
        return $this->getEntityManager()
        ->createQuery(
        'SELECT p FROM QuestionKeyBundle:NodeOption p WHERE p.node = :node ORDER BY p.sort ASC'
        )
        ->setParameter('node', $node)
        ->getResult();
    }


    public function findActiveIncomingNodeOptionsForNode(Node $node)
    {
        return $this->getEntityManager()
        ->createQuery(
        'SELECT p FROM QuestionKeyBundle:NodeOption p WHERE p.destinationNode = :node ORDER BY p.sort ASC'
        )
        ->setParameter('node', $node)
        ->getResult();
    }

    public function findAllNodeOptionsForTreeVersion(TreeVersion $treeVersion)
    {
        return $this->getEntityManager()
        ->createQuery(
        'SELECT no FROM QuestionKeyBundle:NodeOption no '.
        ' WHERE no.treeVersion = :treeVersion '
        )
        ->setParameter('treeVersion', $treeVersion)
        ->getResult();
    }

}
