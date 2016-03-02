<?php

namespace QuestionKeyBundle\Tests;

use QuestionKeyBundle\CopyNewVersionOfTree;
use QuestionKeyBundle\Entity\LibraryContent;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeHasLibraryContent;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\PurgeTreeVersion;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class PurgeTreeVersionTest extends BaseTestWithDataBase
{

    function testPurge1() {

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

        $nodeHasLibraryContentRepo = $this->em->getRepository('QuestionKeyBundle:NodeHasLibraryContent');
        $nodeHasLibraryContentRepo->addLibraryContentToNode($content1, $node);



        ################################ Start Purging
        $purgeTreeVersion = new PurgeTreeVersion($this->em, $treeVersion);
        $purgeTreeVersion->go();


    }

}

