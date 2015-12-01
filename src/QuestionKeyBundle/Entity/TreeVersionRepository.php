<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

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

}
