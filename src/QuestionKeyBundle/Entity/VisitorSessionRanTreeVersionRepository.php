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

    public function doesPublicIdExist($id, VisitorSession $visitorSession)
    {
        if ($visitorSession->getId()) {
            $tvs =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT vsrtv FROM QuestionKeyBundle:VisitorSessionRanTreeVersion vsrtv'.
                    ' WHERE vsrtv.visitorSession = :vs AND vsrtv.publicId = :public_id'
                    )
                ->setParameter('vs', $visitorSession)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$tvs;
        } else {
            return false;
        }
    }

}
