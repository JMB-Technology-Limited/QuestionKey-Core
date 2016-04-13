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
abstract class BaseSeleniumTest extends BaseTestWithDataBase {


    /**
     * When we do an action that does NOT require loading something over the network before continuing, how long to wait?
     * */
    protected $sleepOnActionNoNetwork = 2;

    /**
     * When we do an action that requires loading something over the network before continuing, how long to wait?
     * */
    protected $sleepOnActionWithNetwork = 10;

    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $host = 'http://localhost:4444/wd/hub';
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());
        $this->driver->manage()->window()->maximize();

    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->driver->close();

    }


}

