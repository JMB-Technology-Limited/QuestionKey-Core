<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


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
            throw new  NotFoundHttpException('Not found');
        }
        // load
        $treeRanRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');
        $this->ranTreeVersion = $treeRanRepo->findOneById($ranTreeVersionId);
        if (!$this->ranTreeVersion) {
            throw new  NotFoundHttpException('Not found');
        }
    }



    public function indexAction($sessionId, $ranTreeVersionId)
    {

        // build
        $return = $this->build($sessionId, $ranTreeVersionId);

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
