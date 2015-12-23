<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


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
            return  new Response( '404' );
        }
        // load
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $this->treeVersion = $treeVersionRepo->findOneBy(array(
            'tree'=>$this->tree,
            'publicId'=>$versionId,
        ));
        if (!$this->treeVersion) {
            return  new Response( '404' );
        }
        // load
        $treeVersionPreviewCodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionPreviewCode');
        $this->treeVersionPreviewCode = $treeVersionPreviewCodeRepo->findOneBy(array(
            'treeVersion'=>$this->treeVersion,
            'code'=>$code,
        ));
        if (!$this->treeVersionPreviewCode) {
            return  new Response( '404' );
        }
        return null;
    }



    public function demoAction($treeId, $versionId, $code)
    {

        // build
        $return = $this->build($treeId, $versionId, $code);
        if ($return) {
            return $return;
        }

        // out
        return $this->render('QuestionKeyBundle:TreeVersionCode:demo.html.twig', array(
            'tree'	=> $this->tree,
            'treeVersion' => $this->treeVersion,
            'code' => $this->treeVersionPreviewCode->getCode(),
        ));

    }


    protected function getObjects()
    {

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $out = array(
            'public_id' => $this->tree->getPublicId(),
            'nodes'=>array(),
            'nodeOptions'=>array(),
            'version' => array(
                'public_id' => $this->treeVersion->getPublicId(),
            ),
        );

        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);
        if ($treeStartingNode) {
            $out['start_node'] = array('id'=>$treeStartingNode->getNode()->getPublicId());
        }

        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $nodeOptionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion);
        foreach($nodes as $node) {
            $outNode = array(
                'id'=>$node->getPublicId(),
                'body_html'=>$node->getBodyHTML(),
                'body_text'=>$node->getBodyText(),
                'title'=>$node->getTitle(),
                'title_previous_answers'=>$node->getTitlePreviousAnswers(),
                'options'=>array(),
            );
            foreach($nodeOptionRepo->findActiveNodeOptionsForNode($node) as $nodeOption) {
                $destNode = $nodeOption->getDestinationNode();
                $outNode['options'][$nodeOption->getPublicId()] = array(
                    'id'=>$nodeOption->getPublicId(),
                );
            }
            $out['nodes'][$node->getPublicId()] =  $outNode;
        }

        foreach($nodeOptionRepo->findAllNodeOptionsForTreeVersion($this->treeVersion) as $nodeOption) {
            $out['nodeOptions'][$nodeOption->getPublicId()] = array(
                'id'=>$nodeOption->getPublicId(),
                'title'=>$nodeOption->getTitle(),
                'body_html'=>$nodeOption->getBodyHTML(),
                'body_text'=>$nodeOption->getBodyText(),
                'node' => array(
                    'id' => $nodeOption->getNode()->getPublicId(),
                ),
                'destination_node' => array(
                    'id' => $nodeOption->getDestinationNode()->getPublicId(),
                ),
            );
        }

        return $out;

    }


    public function dataJSONAction($treeId, $versionId, $code)
    {

        // build
        $return = $this->build($treeId, $versionId, $code);
        if ($return) {
            return $return;
        }

        // data
        $data = $this->getObjects();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;


    }


}
