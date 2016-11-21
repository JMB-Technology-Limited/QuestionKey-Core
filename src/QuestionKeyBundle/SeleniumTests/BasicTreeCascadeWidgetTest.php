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
class BasicTreeCascadeWidgetTest extends BaseSeleniumTest {



    function test1() {



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
        $this->driver->get('http://localhost/app_dev.php/tree/tree/demo/cascade');

        $startLink = $this->driver->findElement(WebDriverBy::id('StartTreeLink'));
        $this->assertEquals('Start Tree!', $startLink->getText());

        // ######################################################## Start Tree

        $startLink->click();

        sleep($this->sleepOnActionWithNetwork);

        $nodeTitle = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'));
        $this->assertEquals(0, strpos($nodeTitle->getText(), 'START HERE'));

        // ######################################################## LOAD PAGE

        $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::cssSelector('option[value="option"]'))
            ->click();

        sleep($this->sleepOnActionNoNetwork);

        $nodeTitle = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'));
        $this->assertGreaterThan(0, strpos($nodeTitle->getText(), 'END HERE'));


    }





}

