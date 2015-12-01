<?php

namespace QuestionKeyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class QuestionKeyBundle extends Bundle
{

    static function createKey($minLength = 10, $maxLength = 100)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string ='';
        $length = mt_rand($minLength, $maxLength);
        for ($p = 0; $p < $length; $p++)
        {
            $string .= $characters[mt_rand(0, strlen($characters)-1)];
        }
        return $string;
    }

}
