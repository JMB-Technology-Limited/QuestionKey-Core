<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class TreeVersionPublishedRepository extends EntityRepository
{

    public function findAllForTree(Tree $tree)
    {
        return $this->getEntityManager()
            ->createQuery(
                ' SELECT tvp FROM QuestionKeyBundle:TreeVersionPublished tvp '.
                ' JOIN tvp.treeVersion tv '.
                ' WHERE tv.tree = :tree '.
                ' ORDER BY tvp.publishedAt ASC'
                )
            ->setParameter('tree', $tree)
            ->getResult();
    }



}
