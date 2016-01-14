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


    public function hasActiveIncomingNodeOptionsForNode(Node $node)
    {
        $r =  $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(p) FROM QuestionKeyBundle:NodeOption p WHERE p.destinationNode = :node '
            )
            ->setParameter('node', $node)
            ->getResult();
        return ($r[0][1]) > 0;
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

    public function doesPublicIdExist($id, TreeVersion $treeVersion)
    {
        if ($treeVersion->getId()) {
            $s =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT no FROM QuestionKeyBundle:NodeOption no'.
                    ' WHERE no.treeVersion = :treeVersion AND no.publicId = :public_id'
                    )
                ->setParameter('treeVersion', $treeVersion)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$s;
        } else {
            return false;
        }
    }

    public function getNextSortValueForNode(Node $node) {
        $s =  $this->getEntityManager()
            ->createQuery(
                ' SELECT MAX(no.sort) AS sort FROM QuestionKeyBundle:NodeOption no'.
                ' WHERE no.node = :node '.
                ' GROUP BY no.node'
                )
            ->setParameter('node', $node)
            ->getResult();

        return $s ? $s[0]['sort'] + 10 : 10;
    }

}
