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

    public function findByLibraryContent(LibraryContent $libraryContent)
    {
        return $this->getEntityManager()
            ->createQuery(
            'SELECT n FROM QuestionKeyBundle:Node n '.
            'LEFT JOIN n.hasLibraryContents nhlc '.
            'WHERE nhlc.libraryContent = :library_content '.
            'ORDER BY n.title ASC'
            )
            ->setParameter('library_content', $libraryContent)
            ->getResult();
    }

    public function doesPublicIdExist($id, TreeVersion $treeVersion)
    {
        if ($treeVersion->getId()) {
            $s =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT n FROM QuestionKeyBundle:Node n'.
                    ' WHERE n.treeVersion = :treeVersion AND n.publicId = :public_id'
                    )
                ->setParameter('treeVersion', $treeVersion)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$s;
        } else {
            return false;
        }
    }

    public function getCountNodesForTreeVersion(TreeVersion $treeVersion) {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(n.id) FROM QuestionKeyBundle:Node n '.
                'WHERE n.treeVersion = :tree_version  '
            )
            ->setParameter('tree_version', $treeVersion)
            ->getSingleScalarResult();
    }


}
