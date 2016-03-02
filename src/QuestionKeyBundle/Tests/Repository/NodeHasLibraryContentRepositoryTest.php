<?php

namespace QuestionKeyBundle\Tests\Repository;

use QuestionKeyBundle\Entity\LibraryContent;
use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\NodeOptionRepository;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\Tests\BaseTestWithDataBase;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeHasLibraryContentRepositoryTest extends BaseTestWithDataBase
{

    public function testAddAndRemove() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setOwner($user);
        $tree->setPublicId('tree');
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');
        $treeVersion->setFeatureLibraryContent(true);
        $this->em->persist($treeVersion);

        $node = new Node();
        $node->setTreeVersion($treeVersion);
        $node->setPublicId('start');
        $this->em->persist($node);

        $content1 = new LibraryContent();
        $content1->setTitleAdmin('cats');
        $content1->getBodyText('cats are nice');
        $content1->setTreeVersion($treeVersion);
        $this->em->persist($content1);

        $this->em->flush();


        $libraryContentRepo = $this->em->getRepository('QuestionKeyBundle:LibraryContent');
        $nodeHasLibraryContentRepo = $this->em->getRepository('QuestionKeyBundle:NodeHasLibraryContent');

        // #################################################### TEST NO CONTENT

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(0, count($contentWeGot));




        // #################################################### TEST ADD

        $nodeHasLibraryContentRepo->addLibraryContentToNode($content1, $node);

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(1, count($contentWeGot));
        $this->assertEquals($content1->getId(), $contentWeGot[0]->getId());


        // #################################################### TEST ADD AGAIN IS FINE


        $nodeHasLibraryContentRepo->addLibraryContentToNode($content1, $node);

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(1, count($contentWeGot));
        $this->assertEquals($content1->getId(), $contentWeGot[0]->getId());

        // #################################################### TEST REMOVE

        $nodeHasLibraryContentRepo->removeLibraryContentFromNode($content1, $node);

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(0, count($contentWeGot));


        // #################################################### TEST REMOVE AGAIN IS FINE

        $nodeHasLibraryContentRepo->removeLibraryContentFromNode($content1, $node);

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(0, count($contentWeGot));

    }

    public function testAddTwoCheckOrder() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setOwner($user);
        $tree->setPublicId('tree');
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');
        $treeVersion->setFeatureLibraryContent(true);
        $this->em->persist($treeVersion);

        $node = new Node();
        $node->setTreeVersion($treeVersion);
        $node->setPublicId('start');
        $this->em->persist($node);

        $content1 = new LibraryContent();
        $content1->setTitleAdmin('cats');
        $content1->getBodyText('cats are nice');
        $content1->setTreeVersion($treeVersion);
        $this->em->persist($content1);

        $content2 = new LibraryContent();
        $content2->setTitleAdmin('cats');
        $content2->getBodyText('cats are evil');
        $content2->setTreeVersion($treeVersion);
        $this->em->persist($content2);

        $this->em->flush();


        $libraryContentRepo = $this->em->getRepository('QuestionKeyBundle:LibraryContent');
        $nodeHasLibraryContentRepo = $this->em->getRepository('QuestionKeyBundle:NodeHasLibraryContent');

        // #################################################### TEST NO CONTENT

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(0, count($contentWeGot));


        // #################################################### TEST ADD FIRST

        $nodeHasLibraryContentRepo->addLibraryContentToNode($content1, $node);

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(1, count($contentWeGot));
        $this->assertEquals($content1->getId(), $contentWeGot[0]->getId());

        $nodeHasLibraryContent = $nodeHasLibraryContentRepo->findOneBy(array('node'=>$node, 'libraryContent'=>$content1));
        $this->assertNotNull($nodeHasLibraryContent);
        $this->assertEquals(0, $nodeHasLibraryContent->getSort());


        // #################################################### TEST ADD SECOND


        $nodeHasLibraryContentRepo->addLibraryContentToNode($content2, $node);

        $contentWeGot = $libraryContentRepo->findForNode($node);
        $this->assertEquals(2, count($contentWeGot));

        $this->assertEquals($content1->getId(), $contentWeGot[0]->getId());

        $this->assertEquals($content2->getId(), $contentWeGot[1]->getId());


        $nodeHasLibraryContent = $nodeHasLibraryContentRepo->findOneBy(array('node'=>$node, 'libraryContent'=>$content1));
        $this->assertNotNull($nodeHasLibraryContent);
        $this->assertEquals(0, $nodeHasLibraryContent->getSort());


        $nodeHasLibraryContent = $nodeHasLibraryContentRepo->findOneBy(array('node'=>$node, 'libraryContent'=>$content2));
        $this->assertNotNull($nodeHasLibraryContent);
        $this->assertEquals(1, $nodeHasLibraryContent->getSort());



    }


}

