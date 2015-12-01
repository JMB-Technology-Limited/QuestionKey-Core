<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminVisitorSessionController extends Controller
{

    protected $session;

    protected function build($sessionId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSession');
        $this->session = $treeRepo->findOneById($sessionId);
        if (!$this->session) {
            return  new Response( '404' );
        }
        return null;
    }



    public function indexAction($sessionId)
    {


        // build
        $return = $this->build($sessionId);
        if ($return) {
            return $return;
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $ranTreeRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');
        $treesRan = $ranTreeRepo->findRanTreesForVisitorSessions($this->session);

        return $this->render('QuestionKeyBundle:AdminVisitorSession:index.html.twig', array(
            'session'=>$this->session,
            'treeVersionsRan'=>$treesRan,
        ));


    }


}
