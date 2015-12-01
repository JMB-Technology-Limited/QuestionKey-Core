<?php

namespace QuestionKeyBundle\Tests\StackTraceForNode;

use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\Tests\BaseTestWithDataBase;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class SimpleTest extends BaseTestWithDataBase {


    function testTwoNodes() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setOwner($user);
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $this->em->persist($treeVersion);

        $startNode = new Node();
        $startNode->setTreeVersion($treeVersion);
        $startNode->setPublicId('start');
        $this->em->persist($startNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $otherEndNode = new Node();
        $otherEndNode->setTreeVersion($treeVersion);
        $otherEndNode->setPublicId('otherend');
        $this->em->persist($otherEndNode);

        $nodeOption = new NodeOption();
        $nodeOption->setNode($startNode);
        $nodeOption->setDestinationNode($endNode);
        $nodeOption->setPublicId('option');
        $this->em->persist($nodeOption);

        $otherNodeOption = new NodeOption();
        $otherNodeOption->setNode($startNode);
        $otherNodeOption->setDestinationNode($otherEndNode);
        $otherNodeOption->setPublicId('optionOther');
        $this->em->persist($otherNodeOption);

        $this->em->flush();

        $tvsn = new TreeVersionStartingNode();
        $tvsn->setNode($startNode);
        $tvsn->setTreeVersion($treeVersion);
        $this->em->persist($tvsn);

        $this->em->flush();

        $process = new GetStackTracesForNode($this->em, $endNode);
        $process->go();

        $stackTraces = $process->getStackTraces();

        $this->assertEquals(1, count($stackTraces));

        $stackTrace = $stackTraces[0];

        $data = $stackTrace->getData();

        $this->assertEquals(2, count($data));

        $this->assertEquals('d6151cdb913fbf06a614965109b97355', $stackTrace->getPublicId());

        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($startNode->getId(), $dataRow['node']->getId());
        $this->assertNotNull($dataRow['nodeOption']);
        $this->assertEquals($nodeOption->getId(), $dataRow['nodeOption']->getId());

        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($endNode->getId(), $dataRow['node']->getId());
        $this->assertNull($dataRow['nodeOption']);



    }


    function testThreeNodesInLine() {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree');
        $tree->setOwner($user);
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $this->em->persist($treeVersion);

        $startNode = new Node();
        $startNode->setTreeVersion($treeVersion);
        $startNode->setPublicId('start');
        $this->em->persist($startNode);

        $middleNode = new Node();
        $middleNode->setTreeVersion($treeVersion);
        $middleNode->setPublicId('middle');
        $this->em->persist($middleNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $nodeOption1 = new NodeOption();
        $nodeOption1->setNode($startNode);
        $nodeOption1->setDestinationNode($middleNode);
        $nodeOption1->setPublicId('no1');
        $this->em->persist($nodeOption1);

        $nodeOption2 = new NodeOption();
        $nodeOption2->setNode($middleNode);
        $nodeOption2->setDestinationNode($endNode);
        $nodeOption2->setPublicId('no2');
        $this->em->persist($nodeOption2);

        $this->em->flush();

        $tvsn = new TreeVersionStartingNode();
        $tvsn->setNode($startNode);
        $tvsn->setTreeVersion($treeVersion);
        $this->em->persist($tvsn);

        $this->em->flush();

        $process = new GetStackTracesForNode($this->em, $endNode);
        $process->go();

        $stackTraces = $process->getStackTraces();

        $this->assertEquals(1, count($stackTraces));

        $stackTrace = $stackTraces[0];

        $this->assertEquals('e49c9061b266fe01007a004ff2cb7be3', $stackTrace->getPublicId());

        $data = $stackTrace->getData();

        $this->assertEquals(3, count($data));


        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($startNode->getId(), $dataRow['node']->getId());
        $this->assertNotNull($dataRow['nodeOption']);
        $this->assertEquals($nodeOption1->getId(), $dataRow['nodeOption']->getId());

        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($middleNode->getId(), $dataRow['node']->getId());
        $this->assertNotNull($dataRow['nodeOption']);
        $this->assertEquals($nodeOption2->getId(), $dataRow['nodeOption']->getId());


        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($endNode->getId(), $dataRow['node']->getId());
        $this->assertNull($dataRow['nodeOption']);

    }

}
