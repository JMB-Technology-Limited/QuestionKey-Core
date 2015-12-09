<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;

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


}
