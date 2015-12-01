<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class TreeController extends Controller
{

    protected $tree;

    protected function build($treeId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $this->tree = $treeRepo->findOneByPublicId($treeId);
        if (!$this->tree) {
            return  new Response( '404' );
        }
        return null;
    }



    public function demoAction($treeId)
    {


        // build
        $return = $this->build($treeId);
        if ($return) {
            return $return;
        }


        // out
        return $this->render('QuestionKeyBundle:Tree:demo.html.twig', array(
            'tree'	=> $this->tree,
        ));

    }

}
