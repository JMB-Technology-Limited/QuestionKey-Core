<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionPublishedListController extends Controller
{


    protected $tree;

    protected function build($treeId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $this->tree = $treeRepo->findOneById($treeId);
        if (!$this->tree) {
            return  new Response( '404' );
        }
        return null;
    }



    public function indexAction($treeId)
    {


        // build
        $return = $this->build($treeId);
        if ($return) {
            return $return;
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $tvRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionPublished');

        $tvsp = $tvRepo->findAllForTree($this->tree);

        return $this->render('QuestionKeyBundle:AdminTreeVersionPublishedList:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersionsPublished'=>$tvsp,
        ));


    }



}
