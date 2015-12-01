<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class VisitorSessionOnNodeRepository extends EntityRepository
{

    public function findNodesForVisitorSessionRanTreeVersion(VisitorSessionRanTreeVersion $visitorSessionRanTreeVersion)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM QuestionKeyBundle:VisitorSessionOnNode p WHERE p.sessionRanTreeVersion = :sessionRanTreeVersion ORDER BY p.createdAt ASC'
                )
            ->setParameter('sessionRanTreeVersion', $visitorSessionRanTreeVersion)
            ->getResult();
    }

}
