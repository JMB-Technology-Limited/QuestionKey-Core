<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;



/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeHasLibraryContentIfVariableRepository extends EntityRepository
{




    public function doesPublicIdExist($id, Node $node)
    {
        if ($node->getId()) {
            $s =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT nhlciv FROM QuestionKeyBundle:NodeHasLibraryContentIfVariable nhlciv'.
                    ' WHERE nhlciv.node = :node AND nhlciv.publicId = :public_id'
                )
                ->setParameter('node', $node)
                ->setParameter('public_id', $id)
                ->getResult();

            return (boolean)$s;
        } else {
            return false;
        }
    }

}

