<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\TreeVersion;

/**
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class TreeVersionPreviewCodeRepository extends EntityRepository
{


    public function doesCodeExist($code, TreeVersion $treeVersion)
    {
        if ($treeVersion->getId()) {
            $tvs =  $this->getEntityManager()
                ->createQuery(
                ' SELECT tvpc FROM QuestionKeyBundle:TreeVersionPreviewCode tvpc'.
                ' WHERE tvpc.treeVersion = :treeVersion AND tvpc.code = :code'
                )
                ->setParameter('treeVersion', $treeVersion)
                ->setParameter('code', $code)
                ->getResult();

            return (boolean)$tvs;
        } else {
            return false;
        }
    }



}
