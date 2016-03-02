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
            $nodeHasLibraryContent = new NodeHasLibraryContent();
            $nodeHasLibraryContent->setNode($node);
            $nodeHasLibraryContent->setLibraryContent($libraryContent);
            $nodeHasLibraryContent->setSort(0);
            $this->getEntityManager()->persist($nodeHasLibraryContent);
            $this->getEntityManager()->flush($nodeHasLibraryContent);
        }

    }


    public function removeLibraryContentFromNode(LibraryContent $libraryContent, Node $node) {

        $nodeHasLibraryContent = $this->findOneBy(array('node' => $node, 'libraryContent' => $libraryContent));
        if ($nodeHasLibraryContent) {
            $this->getEntityManager()->remove($nodeHasLibraryContent);
            $this->getEntityManager()->flush($nodeHasLibraryContent);
        }

    }

}