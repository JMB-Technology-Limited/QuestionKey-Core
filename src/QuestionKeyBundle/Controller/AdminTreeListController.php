<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeListController extends Controller
{

    public function indexAction()
    {

        $doctrine = $this->getDoctrine()->getManager();
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $trees = $treeRepo->findAll();

        return $this->render('QuestionKeyBundle:AdminTreeList:index.html.twig', array(
            'trees'=>$trees,
        ));


    }

}
