<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminVisitorSessionRanTreeVersionController extends Controller
{

    protected $session;

    protected $ranTreeVersion;

    protected function build($sessionId, $ranTreeVersionId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSession');
        $this->session = $treeRepo->findOneById($sessionId);
        if (!$this->session) {
            return  new Response( '404' );
        }
        // load
        $treeRanRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');
        $this->ranTreeVersion = $treeRanRepo->findOneById($ranTreeVersionId);
        if (!$this->ranTreeVersion) {
            return  new Response( '404' );
        }

        return null;
    }



    public function indexAction($sessionId, $ranTreeVersionId)
    {

        // build
        $return = $this->build($sessionId, $ranTreeVersionId);
        if ($return) {
            return $return;
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $onNodesRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionOnNode');
        $onNodes = $onNodesRepo->findNodesForVisitorSessionRanTreeVersion($this->ranTreeVersion);

        return $this->render('QuestionKeyBundle:AdminVisitorSessionRanTreeVersion:index.html.twig', array(
            'session'=>$this->session,
            'ranTreeVersion'=>$this->ranTreeVersion,
            'onNodes' => $onNodes,
        ));

    }


}
