<?php

namespace QuestionKeyBundle\Tests\Repository;

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
class NodeOptionRepositoryTest extends BaseTestWithDataBase {


    function testGetNextSortValueForNode1() {

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
        $this->em->persist($treeVersion);

        $node = new Node();
        $node->setTreeVersion($treeVersion);
        $node->setPublicId('start');
        $this->em->persist($node);

        $this->em->flush();

        // #################################################### TEST NO NODES


        $nodeOptionRepo = $this->em->getRepository('QuestionKeyBundle:NodeOption');
        $nextSort = $nodeOptionRepo->getNextSortValueForNode($node);
        $this->assertEquals(10, $nextSort);

        // #################################################### Add A NodeOptionRepository

        $nodeOption = new NodeOption();
        $nodeOption->setTreeVersion($treeVersion);
        $nodeOption->setNode($node);
        $nodeOption->setDestinationNode($node);
        $nodeOption->setPublicId('no1');
        $nodeOption->setSort($nextSort);
        $this->em->persist($nodeOption);

        $this->em->flush();

        // #################################################### TEST NO NODES


        $nodeOptionRepo = $this->em->getRepository('QuestionKeyBundle:NodeOption');
        $nextSort = $nodeOptionRepo->getNextSortValueForNode($node);
        $this->assertEquals(20, $nextSort);

        // #################################################### Add A NodeOptionRepository

        $nodeOption = new NodeOption();
        $nodeOption->setTreeVersion($treeVersion);
        $nodeOption->setNode($node);
        $nodeOption->setDestinationNode($node);
        $nodeOption->setPublicId('no2');
        $nodeOption->setSort($nextSort);
        $this->em->persist($nodeOption);

        $this->em->flush();


        // #################################################### TEST NO NODES


        $nodeOptionRepo = $this->em->getRepository('QuestionKeyBundle:NodeOption');
        $nextSort = $nodeOptionRepo->getNextSortValueForNode($node);
        $this->assertEquals(30, $nextSort);

        // #################################################### Add A NodeOptionRepository

        $nodeOption = new NodeOption();
        $nodeOption->setTreeVersion($treeVersion);
        $nodeOption->setNode($node);
        $nodeOption->setDestinationNode($node);
        $nodeOption->setPublicId('no3');
        $nodeOption->setSort($nextSort);
        $this->em->persist($nodeOption);

        $this->em->flush();


        // #################################################### TEST NO NODES


        $nodeOptionRepo = $this->em->getRepository('QuestionKeyBundle:NodeOption');
        $nextSort = $nodeOptionRepo->getNextSortValueForNode($node);
        $this->assertEquals(40, $nextSort);

        // #################################################### Add A NodeOptionRepository

        $nodeOption = new NodeOption();
        $nodeOption->setTreeVersion($treeVersion);
        $nodeOption->setNode($node);
        $nodeOption->setDestinationNode($node);
        $nodeOption->setPublicId('no4');
        $nodeOption->setSort($nextSort);
        $this->em->persist($nodeOption);

        $this->em->flush();




    }

}
