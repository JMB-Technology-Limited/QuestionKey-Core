<?php

namespace QuestionKeyBundle\Tests\GetUnreachableBitsOfTree;

use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\GetUnreachableBitsOfTree;
use QuestionKeyBundle\Tests\BaseTestWithDataBase;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class UnReachableTest extends BaseTestWithDataBase {


    function testTwoNodesNoNodeOption() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setPublicId('tree');
        $tree->setOwner($user);
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');
        $this->em->persist($treeVersion);

        $startNode = new Node();
        $startNode->setTreeVersion($treeVersion);
        $startNode->setPublicId('start');
        $this->em->persist($startNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $this->em->flush();

        $tvsn = new TreeVersionStartingNode();
        $tvsn->setNode($startNode);
        $tvsn->setTreeVersion($treeVersion);
        $this->em->persist($tvsn);

        $this->em->flush();


        // TEST
        $process = new GetUnreachableBitsOfTree($this->em, $treeVersion);
        $process->go();

        $unreachableNodes = $process->getUnreachableNodes();
        $this->assertEquals(1, count($unreachableNodes));

        $unreachableNode = $unreachableNodes[0];
        $this->assertEquals($endNode->getId(), $unreachableNode->getId());

    }

    function testTwoNodesNoStartingNode() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setPublicId('tree');
        $tree->setOwner($user);
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');
        $this->em->persist($treeVersion);

        $startNode = new Node();
        $startNode->setTreeVersion($treeVersion);
        $startNode->setPublicId('start');
        $this->em->persist($startNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $nodeOption = new NodeOption();
        $nodeOption->setTreeVersion($treeVersion);
        $nodeOption->setNode($startNode);
        $nodeOption->setDestinationNode($endNode);
        $nodeOption->setPublicId('option');
        $this->em->persist($nodeOption);

        $this->em->flush();

        // TEST
        $process = new GetUnreachableBitsOfTree($this->em, $treeVersion);
        $process->go();

        $unreachableNodes = $process->getUnreachableNodes();
        $this->assertEquals(2, count($unreachableNodes));

        $unreachableNode = $unreachableNodes[0];
        $this->assertEquals($startNode->getId(), $unreachableNode->getId());

        $unreachableNode = $unreachableNodes[1];
        $this->assertEquals($endNode->getId(), $unreachableNode->getId());

    }

    function testTwoNodesInLoop() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setPublicId('tree');
        $tree->setOwner($user);
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');
        $this->em->persist($treeVersion);

        $nodeA = new Node();
        $nodeA->setTreeVersion($treeVersion);
        $nodeA->setPublicId('start');
        $this->em->persist($nodeA);

        $nodeB = new Node();
        $nodeB->setTreeVersion($treeVersion);
        $nodeB->setPublicId('end');
        $this->em->persist($nodeB);

        $nodeAtoB = new NodeOption();
        $nodeAtoB->setTreeVersion($treeVersion);
        $nodeAtoB->setNode($nodeA);
        $nodeAtoB->setDestinationNode($nodeB);
        $nodeAtoB->setPublicId('option1');
        $this->em->persist($nodeAtoB);

        $nodeBtoA = new NodeOption();
        $nodeBtoA->setTreeVersion($treeVersion);
        $nodeBtoA->setNode($nodeB);
        $nodeBtoA->setDestinationNode($nodeA);
        $nodeBtoA->setPublicId('option2');
        $this->em->persist($nodeBtoA);

        $this->em->flush();

        // TEST
        $process = new GetUnreachableBitsOfTree($this->em, $treeVersion);
        $process->go();

        $unreachableNodes = $process->getUnreachableNodes();
        $this->assertEquals(2, count($unreachableNodes));

        $unreachableNode = $unreachableNodes[0];
        $this->assertEquals($nodeA->getId(), $unreachableNode->getId());

        $unreachableNode = $unreachableNodes[1];
        $this->assertEquals($nodeB->getId(), $unreachableNode->getId());

    }


}
