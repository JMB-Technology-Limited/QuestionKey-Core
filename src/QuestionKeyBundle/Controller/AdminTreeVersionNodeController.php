<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\NodeHasLibraryContent;
use QuestionKeyBundle\StatsDateRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminNodeEditType;
use QuestionKeyBundle\Form\Type\AdminNodeOptionNewType;
use QuestionKeyBundle\Form\Type\AdminNodeMakeStartType;
use QuestionKeyBundle\Form\Type\AdminConfirmDeleteType;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionNodeController extends Controller
{


    protected $tree;
    protected $treeVersion;
    protected $treeVersionEditable;
    protected $node;

    protected function build($treeId, $versionId, $nodeId) {
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
        // load
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $this->node = $nodeRepo->findOneBy(array(
            'treeVersion'=>$this->treeVersion,
            'publicId'=>$nodeId,
        ));
        if (!$this->node) {
            throw new  NotFoundHttpException('Not found');
        }
    }



    public function indexAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeOptionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $nodeOptions = $nodeOptionRepo->findActiveNodeOptionsForNode($this->node);
        $incomingNodeOptions = $nodeOptionRepo->findActiveIncomingNodeOptionsForNode($this->node);

        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);


        $contents = null;
        $nodeHasLibraryContentIfVariables = null;

        if ($this->treeVersion->isFeatureLibraryContent()) {
            $libraryContentRepo = $doctrine->getRepository('QuestionKeyBundle:LibraryContent');
            $contents = $libraryContentRepo->findForNode($this->node);

            if ($this->treeVersion->isFeatureVariables()) {
                $nodeHasLibraryContentIfVariableRepo = $doctrine->getRepository('QuestionKeyBundle:NodeHasLibraryContentIfVariable');
                $nodeHasLibraryContentIfVariables = $nodeHasLibraryContentIfVariableRepo->findBy(array('node' => $this->node));
            }
        }
        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'libraryContents'=>$contents,
            'nodeHasLibraryContentIfVariables' => $nodeHasLibraryContentIfVariables,
            'nodeOptions'=>$nodeOptions,
            'incomingNodeOptions'=>$incomingNodeOptions,
            'isStartNode'=>($treeStartingNode ? ($treeStartingNode->getNode() == $this->node) : false),
            'isTreeVersionEditable'=>$this->treeVersionEditable,
        ));


    }



    public function stacktraceAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);

        //data
        $process = new GetStackTracesForNode($this->getDoctrine()->getManager(), $this->node);
        $process->go();


        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:stacktrace.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'stackTraces'=>$process->getStackTraces(),
        ));


    }


    public function previewAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);

        //data

        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:preview.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
        ));


    }

    public function previewBodyHTMLAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);

        //data
        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:previewBodyHTML.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
        ));


    }


    public function statsAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);


        //data
        $statsDateRange = new StatsDateRange();

        $doctrine = $this->getDoctrine()->getManager();

        $tsrtvRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');


        $doctrine = $this->getDoctrine()->getManager();



        //view
        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:stats.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'dateRange'=>$statsDateRange,
            'countTimesRanIncludedNode'=>$tsrtvRepo->getStatsCountTimesRanIncludedNode($this->node, $statsDateRange),
            'countTimesRanForTree'=>$tsrtvRepo->getStatsCountTimesRanForTree($this->tree, $statsDateRange),
            'countTimesRanForTreeVersion'=>$tsrtvRepo->getStatsCountTimesRanForTreeVersion($this->treeVersion, $statsDateRange),
        ));


    }



}
