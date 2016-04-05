<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\EntityRepository;



/**
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class NodeHasLibraryContentRepository extends EntityRepository
{

    public function addLibraryContentToNode(LibraryContent $libraryContent, Node $node)
    {

        $nodeHasLibraryContent = $this->findOneBy(array('node' => $node, 'libraryContent' => $libraryContent));
        if (!$nodeHasLibraryContent) {

            $currentMax =  $this->getEntityManager()
                ->createQuery(
                    ' SELECT MAX(nhlc.sort) FROM QuestionKeyBundle:NodeHasLibraryContent nhlc'.
                    ' WHERE nhlc.node = :node'
                )
                ->setParameter('node', $node)
                ->getScalarResult();

            $nodeHasLibraryContent = new NodeHasLibraryContent();
            $nodeHasLibraryContent->setNode($node);
            $nodeHasLibraryContent->setLibraryContent($libraryContent);
            $nodeHasLibraryContent->setSort(is_null($currentMax[0][1]) ? 0 : $currentMax[0][1] + 1);
            $this->getEntityManager()->persist($nodeHasLibraryContent);
            $this->getEntityManager()->flush($nodeHasLibraryContent);
        }

    }


    public function removeLibraryContentFromNode(LibraryContent $libraryContent, Node $node) {

        $flush = array();

        $nodeHasLibraryContent = $this->findOneBy(array('node' => $node, 'libraryContent' => $libraryContent));
        if ($nodeHasLibraryContent) {
            $this->getEntityManager()->remove($nodeHasLibraryContent);
            $flush[] = $nodeHasLibraryContent;
        }

        $nhlcivRepo = $this->getEntityManager()->getRepository('QuestionKeyBundle:NodeHasLibraryContentIfVariable');
        foreach($nhlcivRepo->findBy(array('node'=>$node, 'libraryContent'=>$libraryContent)) as $nhlciv) {
            $this->getEntityManager()->remove($nhlciv);
            $flush[] = $nhlciv;
        }

        $this->getEntityManager()->flush($flush);

    }

}

