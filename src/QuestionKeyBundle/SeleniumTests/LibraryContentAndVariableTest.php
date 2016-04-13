<?php

namespace QuestionKeyBundle\SeleniumTests;

use Facebook\WebDriver\WebDriverBy;
use QuestionKeyBundle\Entity\LibraryContent;
use QuestionKeyBundle\Entity\NodeHasLibraryContent;
use QuestionKeyBundle\Entity\NodeHasLibraryContentIfVariable;
use QuestionKeyBundle\Entity\NodeOptionVariableAction;
use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionPublished;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\Entity\Variable;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\Tests\BaseTestWithDataBase;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class LibraryContentAndVariableTest extends BaseSeleniumTest
{


    public function setUp()
    {
        parent::setUp();

        // ################################################ Basic Details

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
        $treeVersion->setFeatureLibraryContent(true);
        $treeVersion->setFeatureVariables(true);
        $this->em->persist($treeVersion);


        // ################################################ Variable


        $variable = new Variable();
        $variable->setTreeVersion($treeVersion);
        $variable->setName('score');
        $variable->setType('Integer');
        $this->em->persist($variable);

        // ################################################ Nodes

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

        // ################################################ Node option for points with variable action

        $nodeOptionPoints = new NodeOption();
        $nodeOptionPoints->setTitle("POINTS");
        $nodeOptionPoints->setTreeVersion($treeVersion);
        $nodeOptionPoints->setNode($startNode);
        $nodeOptionPoints->setDestinationNode($endNode);
        $nodeOptionPoints->setSort(10);
        $nodeOptionPoints->setPublicId('points');
        $this->em->persist($nodeOptionPoints);

        $nodeOptionPointsAction = new NodeOptionVariableAction();
        $nodeOptionPointsAction->setNodeOption($nodeOptionPoints);
        $nodeOptionPointsAction->setVariable($variable);
        $nodeOptionPointsAction->setValue(10);
        $nodeOptionPointsAction->setAction('assign');
        $this->em->persist($nodeOptionPointsAction);



        // ################################################ Node option for no points

        $nodeOptionNoPoints = new NodeOption();
        $nodeOptionNoPoints->setTitle("NO POINTS");
        $nodeOptionNoPoints->setTreeVersion($treeVersion);
        $nodeOptionNoPoints->setNode($startNode);
        $nodeOptionNoPoints->setDestinationNode($endNode);
        $nodeOptionNoPoints->setPublicId('nopoints');
        $nodeOptionNoPoints->setSort(100);
        $this->em->persist($nodeOptionNoPoints);


        // ################################################ Library Content for points


        $libraryContentPoints = new LibraryContent();
        $libraryContentPoints->setTreeVersion($treeVersion);
        $libraryContentPoints->setBodyText('POINTS');
        $this->em->persist($libraryContentPoints);

        $nodeHasLibraryContentPoints = new NodeHasLibraryContent();
        $nodeHasLibraryContentPoints->setSort(0);
        $nodeHasLibraryContentPoints->setNode($endNode);
        $nodeHasLibraryContentPoints->setLibraryContent($libraryContentPoints);
        $this->em->persist($nodeHasLibraryContentPoints);

        $nodeHasLibraryContentPointsIfVariable  = new NodeHasLibraryContentIfVariable();
        $nodeHasLibraryContentPointsIfVariable->setPublicId('points');
        $nodeHasLibraryContentPointsIfVariable->setLibraryContent($libraryContentPoints);
        $nodeHasLibraryContentPointsIfVariable->setNode($endNode);
        $nodeHasLibraryContentPointsIfVariable->setVariable($variable);
        $nodeHasLibraryContentPointsIfVariable->setAction('>');
        $nodeHasLibraryContentPointsIfVariable->setValue(0);
        $this->em->persist($nodeHasLibraryContentPointsIfVariable);



        // ################################################ Library Content for NO points



        $libraryContentNoPoints = new LibraryContent();
        $libraryContentNoPoints->setTreeVersion($treeVersion);
        $libraryContentNoPoints->setBodyText('NO POINTS');
        $this->em->persist($libraryContentNoPoints);

        $nodeHasLibraryContentNoPoints = new NodeHasLibraryContent();
        $nodeHasLibraryContentNoPoints->setSort(0);
        $nodeHasLibraryContentNoPoints->setNode($endNode);
        $nodeHasLibraryContentNoPoints->setLibraryContent($libraryContentNoPoints);
        $this->em->persist($nodeHasLibraryContentNoPoints);

        $nodeHasLibraryContentNoPointsIfVariable  = new NodeHasLibraryContentIfVariable();
        $nodeHasLibraryContentNoPointsIfVariable->setPublicId('nopoints');
        $nodeHasLibraryContentNoPointsIfVariable->setLibraryContent($libraryContentNoPoints);
        $nodeHasLibraryContentNoPointsIfVariable->setNode($endNode);
        $nodeHasLibraryContentNoPointsIfVariable->setVariable($variable);
        $nodeHasLibraryContentNoPointsIfVariable->setAction('==');
        $nodeHasLibraryContentNoPointsIfVariable->setValue(0);
        $this->em->persist($nodeHasLibraryContentNoPointsIfVariable);


        // ################################################ MISC


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

    }

    function testPoints()
    {

        // ######################################################## LOAD PAGE
        $this->driver->get('http://localhost/app_dev.php/tree/tree/demo');

        $startLink = $this->driver->findElement(WebDriverBy::id('StartTreeLink'));
        $this->assertEquals('Start Tree!', $startLink->getText());

        // ######################################################## Start Tree

        $startLink->click();

        sleep($this->sleepOnActionWithNetwork);

        $nodeTitle = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('title'));
        $this->assertEquals('START HERE', $nodeTitle->getText());

        // ######################################################## Click Points

        $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElements(WebDriverBy::className('option'))[0]
            ->click();
        $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::cssSelector('input[type="submit"]'))
            ->click();

        sleep($this->sleepOnActionNoNetwork);

        $nodeTitle = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('title'));
        $this->assertEquals('END HERE', $nodeTitle->getText());

        $nodeBody = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('body'));
        $this->assertEquals('POINTS', $nodeBody->getText());
    }

    function testNoPoints()
    {

        // ######################################################## LOAD PAGE
        $this->driver->get('http://localhost/app_dev.php/tree/tree/demo');

        $startLink = $this->driver->findElement(WebDriverBy::id('StartTreeLink'));
        $this->assertEquals('Start Tree!', $startLink->getText());

        // ######################################################## Start Tree

        $startLink->click();

        sleep($this->sleepOnActionWithNetwork);

        $nodeTitle = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('title'));
        $this->assertEquals('START HERE', $nodeTitle->getText());

        // ######################################################## Click Points

        $elements = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElements(WebDriverBy::className('option'));

        $elements[2]->click();

        $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::cssSelector('input[type="submit"]'))
            ->click();

        sleep($this->sleepOnActionNoNetwork);

        $nodeTitle = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('title'));
        $this->assertEquals('END HERE', $nodeTitle->getText());

        $nodeBody = $this->driver
            ->findElement(WebDriverBy::id('DemoHere'))
            ->findElement(WebDriverBy::className('node'))
            ->findElement(WebDriverBy::className('body'));
        $this->assertEquals('NO POINTS', $nodeBody->getText());
    }

}
