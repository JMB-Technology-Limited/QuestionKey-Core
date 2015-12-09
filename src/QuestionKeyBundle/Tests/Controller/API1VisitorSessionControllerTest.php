<?php

namespace QuestionKeyBundle\Tests\Controller;

use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionPublished;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\Tests\BaseTestWithDataBase;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class API1VisitorSessionControllerTest extends BaseTestWithDataBase {



    function testStartNewSession() {

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

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setPublicId('end');
        $this->em->persist($endNode);

        $nodeOption1 = new NodeOption();
        $nodeOption1->setTreeVersion($treeVersion);
        $nodeOption1->setNode($startNode);
        $nodeOption1->setDestinationNode($endNode);
        $nodeOption1->setPublicId('no1');
        $this->em->persist($nodeOption1);

        $this->em->flush();

        $tvsn = new TreeVersionStartingNode();
        $tvsn->setNode($startNode);
        $tvsn->setTreeVersion($treeVersion);
        $this->em->persist($tvsn);

        $tp = new TreeVersionPublished();
        $tp->setTreeVersion($treeVersion);
        $tp->setPublishedBy($user);
        $tp->setPublishedAt(new \DateTime());
        $this->em->persist($tp);

        $this->em->flush();

        // Make Request
        $client = static::createClient();

        $crawler = $client->request('GET','/api/v1/visitorsession/action.json?tree_id=tree&tree_version_id=version&node_id=start');

        $response = $client->getResponse()->getContent();
        $responseData = json_decode($response);

        // Check Session
        $session = $this->em->getRepository('QuestionKeyBundle:VisitorSession')->findOneByPublicId($responseData->session->id);
        $this->assertNotNull($session);
        $this->assertEquals($responseData->session->id, $session->getPublicId());

        // Check Session Ran Tree
        $sessionRanTree = $this->em->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion')->findOneByPublicId($responseData->session_ran_tree_version->id);
        $this->assertNotNull($sessionRanTree);
        $this->assertEquals($responseData->session_ran_tree_version->id, $sessionRanTree->getPublicId());
        $this->assertEquals($session->getId(), $sessionRanTree->getVisitorSession()->getId());
        $this->assertEquals($treeVersion->getId(), $sessionRanTree->getTreeVersion()->getId());

        // Check Node Stored.

        $sessionOnNodes = $this->em->getRepository('QuestionKeyBundle:VisitorSessionOnNode')->findBy(array('sessionRanTreeVersion'=>$sessionRanTree));
        $this->assertEquals(1, count($sessionOnNodes));

        $sessionOnNode = array_pop($sessionOnNodes);
        $this->assertEquals($startNode->getId(), $sessionOnNode->getNode()->getId());


    }

}
