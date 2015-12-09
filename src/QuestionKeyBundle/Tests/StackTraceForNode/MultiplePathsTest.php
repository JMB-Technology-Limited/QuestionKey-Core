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
class MultiplePathsTest extends BaseTestWithDataBase {


    /**
    *    start
    *    /    \
    *    (1)  (2)
    *    /    \
    *  left  right
    *   \     /
    *   (3)   (4)
    *   \     /
    *    end
    **/
    function testDiamond1() {

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

        $startNode = new Node();
        $startNode->setTreeVersion($treeVersion);
        $startNode->setPublicId('start');
        $this->em->persist($startNode);

        $leftNode = new Node();
        $leftNode->setTreeVersion($treeVersion);
        $leftNode->setPublicId('left');
        $this->em->persist($leftNode);

        $rightNode = new Node();
        $rightNode->setTreeVersion($treeVersion);
        $rightNode->setPublicId('right');
        $this->em->persist($rightNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $nodeOption1 = new NodeOption();
        $nodeOption1->setTreeVersion($treeVersion);
        $nodeOption1->setNode($startNode);
        $nodeOption1->setDestinationNode($leftNode);
        $nodeOption1->setPublicId('no1');
        $this->em->persist($nodeOption1);

        $nodeOption2 = new NodeOption();
        $nodeOption2->setTreeVersion($treeVersion);
        $nodeOption2->setNode($startNode);
        $nodeOption2->setDestinationNode($rightNode);
        $nodeOption2->setPublicId('no2');
        $this->em->persist($nodeOption2);

        $nodeOption3 = new NodeOption();
        $nodeOption3->setTreeVersion($treeVersion);
        $nodeOption3->setNode($leftNode);
        $nodeOption3->setDestinationNode($endNode);
        $nodeOption3->setPublicId('no3');
        $this->em->persist($nodeOption3);

        $nodeOption4 = new NodeOption();
        $nodeOption4->setTreeVersion($treeVersion);
        $nodeOption4->setNode($rightNode);
        $nodeOption4->setDestinationNode($endNode);
        $nodeOption4->setPublicId('no4');
        $this->em->persist($nodeOption4);

        $this->em->flush();

        $tvsn = new TreeVersionStartingNode();
        $tvsn->setNode($startNode);
        $tvsn->setTreeVersion($treeVersion);
        $this->em->persist($tvsn);

        $this->em->flush();

        $process = new GetStackTracesForNode($this->em, $endNode);
        $process->go();

        $stackTraces = $process->getStackTraces();

        $this->assertEquals(2, count($stackTraces));


        // #################################################### STACK TRACE 1

        $stackTrace = array_shift($stackTraces);

        $this->assertEquals('0e5c398d20010437c8a7c5282269e69a', $stackTrace->getPublicId());

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
        $this->assertEquals($leftNode->getId(), $dataRow['node']->getId());
        $this->assertNotNull($dataRow['nodeOption']);
        $this->assertEquals($nodeOption3->getId(), $dataRow['nodeOption']->getId());


        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($endNode->getId(), $dataRow['node']->getId());
        $this->assertNull($dataRow['nodeOption']);

        // #################################################### STACK TRACE 2

        $stackTrace = array_shift($stackTraces);

        $this->assertEquals('10ff10dbc9b10356f7d53ed65b77a5e0', $stackTrace->getPublicId());

        $data = $stackTrace->getData();

        $this->assertEquals(3, count($data));


        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($startNode->getId(), $dataRow['node']->getId());
        $this->assertNotNull($dataRow['nodeOption']);
        $this->assertEquals($nodeOption2->getId(), $dataRow['nodeOption']->getId());

        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($rightNode->getId(), $dataRow['node']->getId());
        $this->assertNotNull($dataRow['nodeOption']);
        $this->assertEquals($nodeOption4->getId(), $dataRow['nodeOption']->getId());


        // DATA
        $dataRow = array_shift($data);
        $this->assertNotNull($dataRow['node']);
        $this->assertEquals($endNode->getId(), $dataRow['node']->getId());
        $this->assertNull($dataRow['nodeOption']);

    }

}
