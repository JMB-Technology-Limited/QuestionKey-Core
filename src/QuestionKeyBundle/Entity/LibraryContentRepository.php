<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;



/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class LibraryContentRepository extends EntityRepository
{



    public function doesPublicIdExist($id, TreeVersion $treeVersion)
    {
        if ($treeVersion->getId()) {
            $s =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT lc FROM QuestionKeyBundle:LibraryContent lc'.
                    ' WHERE lc.treeVersion = :treeVersion AND lc.publicId = :public_id'
                )
                ->setParameter('treeVersion', $treeVersion)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$s;
        } else {
            return false;
        }
    }


    public function findForNode(Node $node) {


        return $this->getEntityManager()
            ->createQuery(
                'SELECT lc FROM QuestionKeyBundle:LibraryContent lc '.
                ' JOIN lc.hasLibraryContents hlc '.
                ' WHERE hlc.node = :node '
            )
            ->setParameter('node', $node)
            ->getResult();

    }


}

