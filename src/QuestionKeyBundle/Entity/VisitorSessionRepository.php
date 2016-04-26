<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\Repository\Builder\VisitorSessionRepositoryBuilder;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class VisitorSessionRepository extends EntityRepository
{


    public function doesPublicIdExist($id)
    {
        $s =  $this->getEntityManager()
            ->createQuery(
                ' SELECT vs FROM QuestionKeyBundle:VisitorSession vs'.
                ' WHERE vs.publicId = :public_id'
                )
            ->setParameter('public_id', $id)
            ->getResult();

        return (boolean)$s;
    }

    public function findByBuilder(VisitorSessionRepositoryBuilder $visitorSessionRepositoryBuilder) {

        $where = array();
        $params = array();

        if ($visitorSessionRepositoryBuilder->getDateRange()) {
            $where[] = ' vs.createdAt > :from AND vs.createdAt < :to';
            $params['from'] = $visitorSessionRepositoryBuilder->getDateRange()->getFrom();
            $params['to'] = $visitorSessionRepositoryBuilder->getDateRange()->getTo();
        }

        $s =  $this->getEntityManager()
            ->createQuery(
                ' SELECT vs FROM QuestionKeyBundle:VisitorSession vs'.
                ' WHERE '. implode(" AND ", $where)
            )
            ->setParameters($params)
            ->getResult();

        return $s;

    }

}
