<?php

namespace QuestionKeyBundle\Tests;

use QuestionKeyBundle\CopyNewVersionOfTree;
use QuestionKeyBundle\Entity\LibraryContent;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeHasLibraryContent;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class CopyNewVersionOfTreeTest extends BaseTestWithDataBase
{

    function testCopy()
    {

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

        $treeVersionOld = new TreeVersion();
        $treeVersionOld->setTree($tree);
        $treeVersionOld->setPublicId('v1');
        $treeVersionOld->setTitleAdmin('v1');
        $this->em->persist($treeVersionOld);

        $treeVersionNew = new TreeVersion();
        $treeVersionNew->setTree($tree);
        $treeVersionNew->setPublicId('v2');
        $treeVersionNew->setTitleAdmin('v2');
        $this->em->persist($treeVersionNew);

        $nodeOld1 = new Node();
        $nodeOld1->setTreeVersion($treeVersionOld);
        $nodeOld1->setPublicId('start');
        $nodeOld1->setTitle('START HERE');
        $this->em->persist($nodeOld1);

        $this->em->flush();

        ##################### COPY

        $copyNewVersionOfTree = new CopyNewVersionOfTree($this->em, $treeVersionOld, $treeVersionNew);
        $copyNewVersionOfTree->go();


        #####################  TEST

        $nodes = $this->em->getRepository('QuestionKeyBundle:Node')->findBy(array('treeVersion'=>$treeVersionNew),array('publicId'=>'ASC'));
        $this->assertEquals(1, count($nodes));

        $node = $nodes[0];
        $this->assertEquals($nodeOld1->getPublicId(), $node->getPublicId());
        $this->assertEquals($nodeOld1->getTitle(), $node->getTitle());
        $this->assertEquals($nodeOld1->getTitleAdmin(), $node->getTitleAdmin());
        $this->assertEquals($nodeOld1->getTitlePreviousAnswers(), $node->getTitlePreviousAnswers());

    }

    function testCopyLibraryContent()
    {

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

        $treeVersionOld = new TreeVersion();
        $treeVersionOld->setTree($tree);
        $treeVersionOld->setPublicId('v1');
        $treeVersionOld->setTitleAdmin('v1');
        $treeVersionOld->setFeatureLibraryContent(true);
        $this->em->persist($treeVersionOld);

        $treeVersionNew = new TreeVersion();
        $treeVersionNew->setTree($tree);
        $treeVersionNew->setPublicId('v2');
        $treeVersionNew->setTitleAdmin('v2');
        $treeVersionNew->setFeatureLibraryContent(true);
        $this->em->persist($treeVersionNew);

        $nodeOld1 = new Node();
        $nodeOld1->setTreeVersion($treeVersionOld);
        $nodeOld1->setPublicId('start');
        $nodeOld1->setTitle('START HERE');
        $this->em->persist($nodeOld1);

        $libraryContentOld1 = new LibraryContent();
        $libraryContentOld1->setTitleAdmin('TEST');
        $libraryContentOld1->setBodyText('test1');
        $libraryContentOld1->setTreeVersion($treeVersionOld);
        $this->em->persist($libraryContentOld1);

        $this->em->flush();

        $this->em->getRepository('QuestionKeyBundle:NodeHasLibraryContent')->addLibraryContentToNode($libraryContentOld1, $nodeOld1);



        ##################### COPY

        $copyNewVersionOfTree = new CopyNewVersionOfTree($this->em, $treeVersionOld, $treeVersionNew);
        $copyNewVersionOfTree->go();


        #####################  TEST NODES DIRECTLY

        $nodes = $this->em->getRepository('QuestionKeyBundle:Node')->findBy(array('treeVersion'=>$treeVersionNew),array('publicId'=>'ASC'));
        $this->assertEquals(1, count($nodes));

        $node = $nodes[0];
        $this->assertEquals($nodeOld1->getPublicId(), $node->getPublicId());
        $this->assertEquals($nodeOld1->getTitle(), $node->getTitle());
        $this->assertEquals($nodeOld1->getTitleAdmin(), $node->getTitleAdmin());
        $this->assertEquals($nodeOld1->getTitlePreviousAnswers(), $node->getTitlePreviousAnswers());

        #####################  TEST LIBRARY CONTENT DIRECTLY

        $libraryContents = $this->em->getRepository('QuestionKeyBundle:LibraryContent')->findBy(array('treeVersion'=>$treeVersionNew),array('publicId'=>'ASC'));
        $this->assertEquals(1, count($libraryContents));

        $libraryContent = $libraryContents[0];
        $this->assertEquals($libraryContentOld1->getTitleAdmin(), $libraryContent->getTitleAdmin());

        #####################  TEST LIBRARY CONTENT LOADING OFF NODE

        $libraryContents = $this->em->getRepository('QuestionKeyBundle:LibraryContent')->findForNode($node);
        $this->assertEquals(1, count($libraryContents));

        $libraryContent = $libraryContents[0];
        $this->assertEquals($libraryContentOld1->getTitleAdmin(), $libraryContent->getTitleAdmin());


    }


}


