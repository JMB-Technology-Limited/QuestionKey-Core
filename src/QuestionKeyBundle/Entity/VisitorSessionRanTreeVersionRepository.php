<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\Repository\Builder\VisitorSessionRanTreeVersionRepositoryBuilder;
use QuestionKeyBundle\StatsDateRange;

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

    public function getStatsCountTimesRanForTree(Tree $tree, StatsDateRange $statsDateRange) {
        $data =  $this->getEntityManager()
            ->createQuery(
                ' SELECT COUNT(vsrtv) FROM QuestionKeyBundle:VisitorSessionRanTreeVersion vsrtv'.
                ' JOIN vsrtv.treeVersion tv '.
                ' WHERE tv.tree = :tree AND vsrtv.createdAt > :from AND vsrtv.createdAt < :to'
            )
            ->setParameter('tree', $tree)
            ->setParameter('from', $statsDateRange->getFrom())
            ->setParameter('to', $statsDateRange->getTo())
            ->getScalarResult();
        return $data[0][1];

    }


    public function getStatsCountTimesRanForTreeVersion(TreeVersion $treeVersion, StatsDateRange $statsDateRange) {
        $data =  $this->getEntityManager()
            ->createQuery(
                ' SELECT COUNT(vsrtv) FROM QuestionKeyBundle:VisitorSessionRanTreeVersion vsrtv'.
                ' WHERE vsrtv.treeVersion = :treeVersion AND vsrtv.createdAt > :from AND vsrtv.createdAt < :to '
            )
            ->setParameter('treeVersion', $treeVersion)
            ->setParameter('from', $statsDateRange->getFrom())
            ->setParameter('to', $statsDateRange->getTo())
            ->getScalarResult();
        return $data[0][1];

    }

    public function getStatsCountTimesRanIncludedNode(Node $node, StatsDateRange $statsDateRange) {

        $data =  $this->getEntityManager()
            ->createQuery(
                'SELECT vsrtv.id AS x FROM QuestionKeyBundle:VisitorSessionRanTreeVersion vsrtv'.
                ' JOIN  vsrtv.onNodes vson'.
                ' WHERE vson.node = :node  AND vsrtv.createdAt > :from AND vsrtv.createdAt < :to  '.
                ' GROUP BY vsrtv.id  '
            )
            ->setParameter('node', $node)
            ->setParameter('from', $statsDateRange->getFrom())
            ->setParameter('to', $statsDateRange->getTo())
            ->getResult();
        return count($data);

    }


    public function findByBuilder(VisitorSessionRanTreeVersionRepositoryBuilder $visitorSessionRanTreeVersionRepositoryBuilder) {

        $where = array();
        $params = array();

        if ($visitorSessionRanTreeVersionRepositoryBuilder->getDateRange()) {
            $where[] = ' vsrtv.createdAt > :from AND vsrtv.createdAt < :to';
            $params['from'] = $visitorSessionRanTreeVersionRepositoryBuilder->getDateRange()->getFrom();
            $params['to'] = $visitorSessionRanTreeVersionRepositoryBuilder->getDateRange()->getTo();
        }

        $s =  $this->getEntityManager()
            ->createQuery(
                ' SELECT vsrtv FROM QuestionKeyBundle:VisitorSessionRanTreeVersion vsrtv'.
                ' WHERE '. implode(" AND ", $where)
            )
            ->setParameters($params)
            ->getResult();

        return $s;

    }

}
