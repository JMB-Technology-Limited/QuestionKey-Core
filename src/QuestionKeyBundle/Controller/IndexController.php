<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class IndexController extends Controller
{

    public function indexAction()
    {


        return $this->render('QuestionKeyBundle:Index:index.html.twig', array(
        ));


    }

    public function youAction()
    {


        return $this->render('QuestionKeyBundle:Index:you.html.twig', array(
        ));


    }

}
