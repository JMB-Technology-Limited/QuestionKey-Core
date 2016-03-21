<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\TreeVersion;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeOptionVariableActionRepository extends EntityRepository
{


    public function findForNodeOption(NodeOption $nodeOption)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT nova FROM QuestionKeyBundle:NodeOptionVariableAction nova WHERE nova.nodeOption = :nodeOption ORDER BY nova.sort ASC'
            )
            ->setParameter('nodeOption', $nodeOption)
            ->getResult();
    }


    public function doesPublicIdExist($id, NodeOption $nodeOption)
    {
        if ($nodeOption->getId()) {
            $tvs =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT nova FROM QuestionKeyBundle:NodeOptionVariableAction nova'.
                    ' WHERE nova.nodeOption = :nodeOption AND nova.publicId = :public_id'
                )
                ->setParameter('nodeOption', $nodeOption)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$tvs;
        } else {
            return false;
        }
    }


}

