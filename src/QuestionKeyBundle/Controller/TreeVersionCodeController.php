<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use QuestionKeyBundle\GetTreeVersionDataObjects;


/**
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class TreeVersionCodeController extends Controller
{

    protected $tree;

    protected $treeVersion;

    protected $treeVersionPreviewCode;

    protected function build($treeId, $versionId, $code) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $this->tree = $treeRepo->findOneByPublicId($treeId);
        if (!$this->tree) {
            throw new  NotFoundHttpException('Not found');
        }
        // load
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $this->treeVersion = $treeVersionRepo->findOneBy(array(
            'tree'=>$this->tree,
            'publicId'=>$versionId,
        ));
        if (!$this->treeVersion) {
            throw new  NotFoundHttpException('Not found');
        }
        // load
        $treeVersionPreviewCodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionPreviewCode');
        $this->treeVersionPreviewCode = $treeVersionPreviewCodeRepo->findOneBy(array(
            'treeVersion'=>$this->treeVersion,
            'code'=>$code,
        ));
        if (!$this->treeVersionPreviewCode) {
            throw new  NotFoundHttpException('Not found');
        }
    }



    public function demoAction($treeId, $versionId, $code)
    {

        // build
        $return = $this->build($treeId, $versionId, $code);

        // out
        return $this->render('QuestionKeyBundle:TreeVersionCode:demo.html.twig', array(
            'tree'	=> $this->tree,
            'treeVersion' => $this->treeVersion,
            'code' => $this->treeVersionPreviewCode->getCode(),
        ));

    }

    public function demoCascadeAction($treeId, $versionId, $code)
    {

        // build
        $return = $this->build($treeId, $versionId, $code);

        // out
        return $this->render('QuestionKeyBundle:TreeVersionCode:demo.cascade.html.twig', array(
            'tree'	=> $this->tree,
            'treeVersion' => $this->treeVersion,
            'code' => $this->treeVersionPreviewCode->getCode(),
        ));

    }

    public function dataJSONAction($treeId, $versionId, $code)
    {

        // build
        $return = $this->build($treeId, $versionId, $code);

        // data
        $doctrine = $this->getDoctrine()->getManager();
        $getTreeVersionDataObjects = new GetTreeVersionDataObjects($this->container, $this->treeVersion, false);
        $data = $getTreeVersionDataObjects->go();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

}
