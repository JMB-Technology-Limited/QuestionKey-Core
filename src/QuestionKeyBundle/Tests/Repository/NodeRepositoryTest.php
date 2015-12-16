<?php

namespace QuestionKeyBundle\Tests\Repository;

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
class NodeRepositoryTest extends BaseTestWithDataBase {


    function testCountNodesForTreeVersion1() {

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

        $middleNode = new Node();
        $middleNode->setTreeVersion($treeVersion);
        $middleNode->setPublicId('middle');
        $this->em->persist($middleNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $this->em->flush();

        // #################################################### TEST


        $nodeRepo = $this->em->getRepository('QuestionKeyBundle:Node');
        $this->assertEquals(3, $nodeRepo->getCountNodesForTreeVersion($treeVersion));


    }

}
