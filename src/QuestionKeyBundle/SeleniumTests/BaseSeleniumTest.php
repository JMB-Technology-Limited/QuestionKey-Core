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


    protected $driver;

    public function setUp()
    {
        parent::setUp();


        $host = 'http://localhost:4444/wd/hub';
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());




    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->driver->close();

    }


}

