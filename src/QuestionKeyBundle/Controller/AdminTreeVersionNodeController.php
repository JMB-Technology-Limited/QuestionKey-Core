<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $this->tree = $treeRepo->findOneById($treeId);
        if (!$this->tree) {
            throw new  NotFoundHttpException('Not found');
        }
        // load
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $this->treeVersion = $treeVersionRepo->findOneBy(array(
            'tree'=>$this->tree,
            'id'=>$versionId,
        ));
        if (!$this->treeVersion) {
            throw new  NotFoundHttpException('Not found');
        }
        $this->treeVersionEditable = !$treeVersionRepo->hasEverBeenPublished($this->treeVersion);
        // load
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $this->node = $nodeRepo->findOneBy(array(
            'treeVersion'=>$this->treeVersion,
            'id'=>$nodeId,
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

        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOptions'=>$nodeOptions,
            'incomingNodeOptions'=>$incomingNodeOptions,
            'isStartNode'=>($treeStartingNode ? ($treeStartingNode->getNode() == $this->node) : false),
            'isTreeVersionEditable'=>$this->treeVersionEditable,
        ));


    }


    public function editAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $form = $this->createForm(new AdminNodeEditType(), $this->node);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($this->node);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array('treeId'=>$this->tree->getId(),'versionId'=>$this->treeVersion->getId(),'nodeId'=>$this->node->getId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:edit.html.twig', array(
            'form' => $form->createView(),
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
        ));


    }

    public function deleteAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $form = $this->createForm(new AdminConfirmDeleteType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();

                // node options
                $nodeOptionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
                foreach($nodeOptionRepo->findBy(array('node'=>$this->node)) as $nodeOption) {
                    $doctrine->remove($nodeOption);
                }
                foreach($nodeOptionRepo->findBy(array('destinationNode'=>$this->node)) as $nodeOption) {
                    $doctrine->remove($nodeOption);
                }

                // starting node
                $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
                $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);
                if ($treeStartingNode && $treeStartingNode->getNode() == $this->node) {
                    $doctrine->remove($treeStartingNode);
                }

                // node and go
                $doctrine->remove($this->node);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_show', array('treeId'=>$this->tree->getId(),'versionId'=>$this->treeVersion->getId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:delete.html.twig', array(
            'form' => $form->createView(),
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
        ));


    }


    public function makeStartAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);

        $form = $this->createForm(new AdminNodeMakeStartType(), $this->node);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $treeStartingNodeRepo->setAsStartingNode($this->node);
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array('treeId'=>$this->tree->getId(),'versionId'=>$this->treeVersion->getId(),'nodeId'=>$this->node->getId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:makeStart.html.twig', array(
            'form' => $form->createView(),
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'currentStartNode'=>($treeStartingNode ? $treeStartingNode->getNode() : null),
        ));


    }

    public function newOptionAction($treeId, $versionId, $nodeId)
    {


        // build
        $return = $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeOptionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $form = $this->createForm(new AdminNodeOptionNewType($this->node, $nodeOptionRepo->getNextSortValueForNode($this->node) ));
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $nodeOption = new NodeOption();
                $nodeOption->setTreeVersion($this->treeVersion);
                $nodeOption->setNode($this->node);
                $nodeOption->setTitle($form->get('title')->getData());
                $nodeOption->setBodyText($form->get('body_text')->getData());
                $nodeOption->setBodyHTML($form->get('body_html')->getData());
                $nodeOption->setSort($form->get('sort')->getData());
                $nodeOption->setDestinationNode($form->get('destination_node')->getData());
                $doctrine->persist($nodeOption);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array('treeId'=>$this->tree->getId(),'versionId'=>$this->treeVersion->getId(),'nodeId'=>$this->node->getId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNode:newOption.html.twig', array(
            'form' => $form->createView(),
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
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

}
