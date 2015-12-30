<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use QuestionKeyBundle\GetTreeVersionDataObjects;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class API1TreeController extends Controller
{

    protected $tree;

    protected $treeVersion;

    protected function build($treeId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $this->tree = $treeRepo->findOneByPublicId($treeId);
        if (!$this->tree) {
            throw new NotFoundHttpException();
        }
        // load
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $this->treeVersion = $treeVersionRepo->findPublishedVersionForTree($this->tree);
        if (!$this->treeVersion) {
            throw new NotFoundHttpException();
        }
    }

    public function dataJSONPAction($treeId, Request $request)
    {

        $this->build($treeId);

        $doctrine = $this->getDoctrine()->getManager();
        $getTreeVersionDataObjects = new GetTreeVersionDataObjects($this->container, $this->treeVersion, false);
        $data = $getTreeVersionDataObjects->go();

        $func  = $request->query->get('callback');
        if (!$func) {
            $func='callback';
        }

        $response = new Response($func . "(" . json_encode($data) . ")");
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;

    }

    public function dataJSONAction($treeId)
    {

        $this->build($treeId);

        $doctrine = $this->getDoctrine()->getManager();
        $getTreeVersionDataObjects = new GetTreeVersionDataObjects($this->container, $this->treeVersion, false);
        $data = $getTreeVersionDataObjects->go();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

}
