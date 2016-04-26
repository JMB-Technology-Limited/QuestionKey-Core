<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\LibraryContent;
use QuestionKeyBundle\Entity\Variable;
use QuestionKeyBundle\Form\Type\AdminLibraryContentNewType;
use QuestionKeyBundle\Form\Type\AdminVariableNewType;
use QuestionKeyBundle\GetUnreachableBitsOfTree;
use QuestionKeyBundle\ImportExport\ExportTreeVersionJSON;
use QuestionKeyBundle\StatsDateRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\TreeVersionPublished;
use QuestionKeyBundle\Entity\TreeVersionPreviewCode;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Form\Type\AdminTreeNewVersionType;
use QuestionKeyBundle\CopyNewVersionOfTree;
use QuestionKeyBundle\Form\Type\AdminTreeVersionEditType;
use QuestionKeyBundle\Form\Type\AdminNodeNewType;
use QuestionKeyBundle\Form\Type\AdminTreeVersionPublishType;
use QuestionKeyBundle\GetTreeVersionDataObjects;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionController extends Controller
{


    protected $tree;

    protected $treeVersion;

    protected $treeVersionEditable;

    protected function build($treeId, $versionId) {
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
        $this->treeVersionEditable = !$treeVersionRepo->hasEverBeenPublished($this->treeVersion);
    }



    public function indexAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);

        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $treeVersionPublished = $treeVersionRepo->findPublishedVersionForTree($this->tree);

        return $this->render('QuestionKeyBundle:AdminTreeVersion:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'startNode'=>($treeStartingNode && $treeStartingNode->getNode() ? $treeStartingNode->getNode() : null),
            'isPublishedVersion'=>($treeVersionPublished ? $treeVersionPublished == $this->treeVersion : null),
            'isTreeVersionEditable'=>$this->treeVersionEditable,
        ));


    }

    public function nodeListAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');

        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion);
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);

        return $this->render('QuestionKeyBundle:AdminTreeVersion:nodeList.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'nodes'=>$nodes,
            'startNode'=>($treeStartingNode && $treeStartingNode->getNode() ? $treeStartingNode->getNode() : null),
            'isTreeVersionEditable'=>$this->treeVersionEditable,
        ));


    }

    public function variableListAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Variable');

        $variables = $nodeRepo->findByTreeVersion($this->treeVersion);

        return $this->render('QuestionKeyBundle:AdminTreeVersion:variableList.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'variables'=>$variables,
        ));


    }


    public function libraryContentListAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $libraryContentRepo = $doctrine->getRepository('QuestionKeyBundle:LibraryContent');

        $contents = $libraryContentRepo->findByTreeVersion($this->treeVersion);

        return $this->render('QuestionKeyBundle:AdminTreeVersion:libraryContentList.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'libraryContents'=>$contents,
        ));


    }

    public function nodeListEndNodesAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');

        $nodes = $nodeRepo->findEndingNodesByTreeVersion($this->treeVersion);

        return $this->render('QuestionKeyBundle:AdminTreeVersion:nodeListEndingNodes.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'nodes'=>$nodes,
            'isTreeVersionEditable'=>$this->treeVersionEditable,
        ));


    }

    public function nodeListUnreacheableNodesAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);

        //data
        $process = new GetUnreachableBitsOfTree($this->getDoctrine(), $this->treeVersion);
        $process->go();
        $nodes = $process->getUnreachableNodes();

        return $this->render('QuestionKeyBundle:AdminTreeVersion:nodeListUnreachableNodes.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'nodes'=>$nodes,
        ));


    }




    public function runAction($treeId, $versionId, Request $request)
    {

        // build
        $return = $this->build($treeId, $versionId);

        // setup
        $doctrine = $this->getDoctrine()->getManager();
        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');

        // data
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);
        if (!$treeStartingNode) {
            return $this->render('QuestionKeyBundle:AdminTreeVersion:run.noStartNode.html.twig', array(
                'tree'=>$this->tree,
                'treeVersion'=>$this->treeVersion,
            ));
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersion:run.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
        ));

    }


    public function dataJSONAction($treeId, $versionId, Request $request)
    {

        // build
        $return = $this->build($treeId, $versionId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $getTreeVersionDataObjects = new GetTreeVersionDataObjects($this->container, $this->treeVersion, true);
        $data = $getTreeVersionDataObjects->go();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    public function graphAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);

        //data
        if ($this->treeVersion->getGraphLayout()) {
            $data = json_decode($this->treeVersion->getGraphLayout());
        } else {
            $data = array('nodes'=>array());
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersion:graph.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'isTreeVersionEditable'=>$this->treeVersionEditable,
            'data'=>$data,
        ));


    }

    public function graphSaveCurrentAction($treeId, $versionId, Request $request)
    {

        // build
        $return = $this->build($treeId, $versionId);

        //data
        $data = $request->request->get('data');
        if ($data) {
            $this->treeVersion->setGraphLayout(json_encode($data));
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($this->treeVersion);
            $doctrine->flush();
            $response = new Response(json_encode(array('result'=>'ok')));
        } else {
            $response = new Response(json_encode(array('result'=>'no_data')));
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    public function getPreviewLinkAction($treeId, $versionId, Request $request)
    {

        // build
        $return = $this->build($treeId, $versionId);

        // Make Link
        $treeVersionPreviewCode = new TreeVersionPreviewCode();
        $treeVersionPreviewCode->setTreeVersion($this->treeVersion);
        $treeVersionPreviewCode->setCreatedBy($this->getUser());

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($treeVersionPreviewCode);
        $doctrine->flush($treeVersionPreviewCode);

        $hasSSL = $this->container->hasParameter('has_ssl') ? $this->container->getParameter('has_ssl') : false;
        $serverHost = $this->container->hasParameter('server_host') ? $this->container->getParameter('server_host') : '';
        $link = '';
        if ($serverHost && $hasSSL) {
            $link .= 'https://'. $serverHost;
        } else if ($serverHost && !$hasSSL) {
            $link .= 'http://'. $serverHost;
        }
        $link .= $this->generateUrl("questionkey_tree_version_preview_demo", array('treeId'=>$this->tree->getPublicId(), 'versionId'=>$this->treeVersion->getPublicId(), 'code'=>$treeVersionPreviewCode->getCode()));

        return $this->render('QuestionKeyBundle:AdminTreeVersion:getPreviewLink.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'isTreeVersionEditable'=>$this->treeVersionEditable,
            'link'=>$link,
        ));

    }

    public function exportAction($treeId, $versionId, Request $request)
    {

        // build
        $return = $this->build($treeId, $versionId);

        $obj = new ExportTreeVersionJSON($this->getDoctrine(), $this->treeVersion);
        $json = $obj->getAsText();

        return $this->render('QuestionKeyBundle:AdminTreeVersion:export.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'isTreeVersionEditable'=>$this->treeVersionEditable,
            'json'=>$json,
        ));

    }

    public function statsAction($treeId, $versionId, Request $request)
    {

        // build
        $return = $this->build($treeId, $versionId);

        //data
        $statsDateRange = new StatsDateRange();
        $statsDateRange->setFromRequest($request);


        $doctrine = $this->getDoctrine()->getManager();

        $tsrtvRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');



        return $this->render('QuestionKeyBundle:AdminTreeVersion:stats.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'isTreeVersionEditable'=>$this->treeVersionEditable,
            'dateRange'=>$statsDateRange,
            'countTimesRanForTree'=>$tsrtvRepo->getStatsCountTimesRanForTree($this->tree, $statsDateRange),
            'countTimesRanForTreeVersion'=>$tsrtvRepo->getStatsCountTimesRanForTreeVersion($this->treeVersion, $statsDateRange),
        ));

    }

}
