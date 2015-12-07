<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        return null;
    }



    protected function getObjects($treeId)
    {

        // build
        $return = $this->build($treeId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $out = array(
            'public_id' => $this->tree->getPublicId(),
            'nodes'=>array(),
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
                'options'=>array(),
            );
            foreach($nodeOptionRepo->findActiveNodeOptionsForNode($node) as $nodeOption) {
                $destNode = $nodeOption->getDestinationNode();
                $outNode['options'][$nodeOption->getPublicId()] = array(
                    'id'=>$nodeOption->getPublicId(),
                    'title'=>$nodeOption->getTitle(),
                    'body_html'=>$nodeOption->getBodyHTML(),
                    'body_text'=>$nodeOption->getBodyText(),
                    'destination_node' => array(
                        'id' => $destNode->getPublicId(),
                        )
                    );
                }
                $out['nodes'][$node->getPublicId()] =  $outNode;
            }

            return $out;

        }




        public function dataJSONPAction($treeId, Request $request)
        {

            $data = $this->getObjects($treeId);

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

            $data = $this->getObjects($treeId);

            $response = new Response(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');

            return $response;


        }


    }
