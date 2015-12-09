<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\TreeVersion;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class TreeVersionRepository extends EntityRepository
{

    public function findPublishedVersionForTree(Tree $tree)
    {

        $tvps =  $this->getEntityManager()
            ->createQuery(
                ' SELECT tvp FROM QuestionKeyBundle:TreeVersionPublished tvp'.
                ' JOIN tvp.treeVersion tv '.
                ' WHERE    tv.tree = :tree '.
                ' ORDER BY tvp.publishedAt DESC '.
                '  '
                )
            ->setMaxResults(1)
            ->setParameter('tree', $tree)
            ->getResult();

        if ($tvps) {
            return $tvps[0]->getTreeVersion();
        } else {
            return null;
        }

    }

    public function doesPublicIdExist($id, Tree $tree)
    {
        if ($tree->getId()) {
            $tvs =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT tv FROM QuestionKeyBundle:TreeVersion tv'.
                    ' WHERE tv.tree = :tree AND tv.publicId = :public_id'
                    )
                ->setParameter('tree', $tree)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$tvs;
        } else {
            return false;
        }
    }

    public function hasEverBeenPublished(TreeVersion $treeVersion) {
        $tvs =  $this->getEntityManager()
            ->createQuery(
                ' SELECT tvp FROM QuestionKeyBundle:TreeVersionPublished tvp'.
                ' WHERE tvp.treeVersion = :treeVersion'
                )
            ->setParameter('treeVersion', $treeVersion)
            ->getResult();

        return (boolean)$tvs;

    }

}
