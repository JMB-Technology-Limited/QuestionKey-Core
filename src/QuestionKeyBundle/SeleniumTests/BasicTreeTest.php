<?php

namespace QuestionKeyBundle\SeleniumTests;

use Facebook\WebDriver\WebDriverBy;
use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionPublished;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\Tests\BaseTestWithDataBase;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class BasicTreeTest extends BaseTestWithDataBase {


    function test1() {



        $host = 'http://localhost:4444/wd/hub';
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());




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
        $startNode->setTitle("START HERE");
        $startNode->setPublicId('start');
        $this->em->persist($startNode);

        $endNode = new Node();
        $endNode->setTreeVersion($treeVersion);
        $endNode->setTitle("END HERE");
        $endNode->setPublicId('end');
        $this->em->persist($endNode);


        $nodeOption = new NodeOption();
        $nodeOption->setTitle("LETS GO HERE");
        $nodeOption->setTreeVersion($treeVersion);
        $nodeOption->setNode($startNode);
        $nodeOption->setDestinationNode($endNode);
        $nodeOption->setPublicId('option');
        $this->em->persist($nodeOption);

        $treeVersionPublished = new TreeVersionPublished();
        $treeVersionPublished->setTreeVersion($treeVersion);
        $treeVersionPublished->setPublishedBy($user);

        $this->em->flush();

        $tvsn = new TreeVersionStartingNode();
        $tvsn->setNode($startNode);
        $tvsn->setTreeVersion($treeVersion);
        $this->em->persist($tvsn);

        $published = new TreeVersionPublished();
        $published->setTreeVersion($treeVersion);
        $this->em->persist($published);

        $this->em->flush();



        // ######################################################## LOAD PAGE
        $driver->get('http://localhost/app_dev.php/tree/tree/demo');

        $startLink = $driver->findElement(WebDriverBy::id('StartTreeLink'));
        $this->assertEquals('Start Tree!', $startLink->getText());

        // ######################################################## Start Tree

        $startLink->click();

        sleep(2);

        $nodeTitle = $driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('title'));
        $this->assertEquals('START HERE', $nodeTitle->getText());


    }

}

