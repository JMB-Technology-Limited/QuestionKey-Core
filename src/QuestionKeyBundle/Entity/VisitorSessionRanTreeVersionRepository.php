<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class VisitorSessionRanTreeVersionRepository extends EntityRepository
{

    public function findRanTreesForVisitorSessions(VisitorSession $visitorSession)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM QuestionKeyBundle:VisitorSessionRanTreeVersion p WHERE p.visitorSession = :visitorSession ORDER BY p.createdAt ASC'
                )
            ->setParameter('visitorSession', $visitorSession)
            ->getResult();
    }

}
