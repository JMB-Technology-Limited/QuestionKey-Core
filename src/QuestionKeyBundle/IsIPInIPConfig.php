<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class IsIPInIPConfig
{

    protected $ipConfig;

    /**
     * IsIPInIPConfig constructor.
     * @param $ipConfig
     */
    public function __construct($ipConfig)
    {
        $this->ipConfig = $ipConfig;
    }


    public function checkIP($ip) {
        if (trim($this->ipConfig) && trim(strtolower($ip)) == trim(strtolower($this->ipConfig))) {
            return true;
        }
        return false;
    }

}
