<?php

namespace QuestionKeyBundle\Tests;

use QuestionKeyBundle\IsIPInIPConfig;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class IsIPInIPConfigTest extends BaseTest
{


    function dataForTestTrue()
    {
        return array(
            array('10.0.0.1','10.0.0.1'),
        );
    }

    /**
     * @dataProvider dataForTestTrue
     */
    function testTrue($config, $ip)
    {
        $isIPInIPConfig = new IsIPInIPConfig($config);
        $this->assertTrue($isIPInIPConfig->checkIP($ip));
    }


    function dataForTestFalse()
    {
        return array(
            array('10.0.0.2','10.0.0.1'),
            array('','10.0.0.1'),
            array('localhost','10.0.0.1'),
        );
    }

    /**
     * @dataProvider dataForTestFalse
     */
    function testFalse($config, $ip)
    {
        $isIPInIPConfig = new IsIPInIPConfig($config);
        $this->assertFalse($isIPInIPConfig->checkIP($ip));
    }

}

