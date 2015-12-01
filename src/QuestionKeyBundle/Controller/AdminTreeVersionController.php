<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\TreeVersionPublished;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Form\Type\AdminTreeNewVersionType;
use QuestionKeyBundle\CopyNewVersionOfTree;
use QuestionKeyBundle\Form\Type\AdminTreeVersionEditType;
use QuestionKeyBundle\Form\Type\AdminNodeNewType;
use QuestionKeyBundle\Form\Type\AdminTreeVersionPublishType;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionController extends Controller
{


    protected $tree;

    protected $treeVersion;

    protected function build($treeId, $versionId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $this->tree = $treeRepo->findOneById($treeId);
        if (!$this->tree) {
            return  new Response( '404' );
        }
        // load
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $this->treeVersion = $treeVersionRepo->findOneBy(array(
            'tree'=>$this->tree,
            'id'=>$versionId,
        ));
        if (!$this->treeVersion) {
            return  new Response( '404' );
        }
        return null;
    }



    public function indexAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

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
        ));


    }

    public function nodeListAction($treeId, $versionId)
    {


        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');

        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion);


        return $this->render('QuestionKeyBundle:AdminTreeVersion:nodeList.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'nodes'=>$nodes,
        ));


    }

    public function nodeListEndNodesAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');

        $nodes = $nodeRepo->findEndingNodesByTreeVersion($this->treeVersion);


        return $this->render('QuestionKeyBundle:AdminTreeVersion:nodeListEndingNodes.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'nodes'=>$nodes,
        ));


    }


    public function newVersionAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

        //process
        $doctrine = $this->getDoctrine()->getManager();
        $newTreeVersion = new TreeVersion();
        $newTreeVersion->setTree($this->tree);

        $form = $this->createForm(new AdminTreeNewVersionType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $newTreeVersion->setTitleAdmin($form->get('title')->getData());
                $doctrine->persist($newTreeVersion);
                $copyNewVersionOfTree = new CopyNewVersionOfTree($doctrine, $this->treeVersion, $newTreeVersion);
                $copyNewVersionOfTree->go();
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_show', array('treeId'=>$this->tree->getId(),'versionId'=>$newTreeVersion->getId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersion:newVersion.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }

    public function editAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

        //data
        $form = $this->createForm(new AdminTreeVersionEditType(), $this->treeVersion);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($this->treeVersion);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_show', array(
                    'treeId'=>$this->tree->getId(),
                    'versionId'=>$this->treeVersion->getId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersion:edit.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }

    public function newNodeAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

        //data
        $form = $this->createForm(new AdminNodeNewType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $node = new Node();
                $node->setTreeVersion($this->treeVersion);
                $node->setTitle($form->get('title')->getData());
                $node->setBodyText($form->get('body_text')->getData());
                $node->setBodyHTML($form->get('body_html')->getData());
                $doctrine->persist($node);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getId(),
                    'versionId'=>$this->treeVersion->getId(),
                    'nodeId'=>$node->getId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersion:newNode.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }



    public function publishAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);
        if ($return) {
            return $return;
        }

        //data
        $form = $this->createForm(new AdminTreeVersionPublishType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $treeVersionPublished = new TreeVersionPublished();
                $treeVersionPublished->setTreeVersion($this->treeVersion);
                $treeVersionPublished->setCommentPublishedAdmin($form->get('comment_admin')->getData());
                $treeVersionPublished->setPublishedBy($this->getUser());
                $doctrine->persist($treeVersionPublished);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_show', array(
                    'treeId'=>$this->tree->getId(),
                    'versionId'=>$this->treeVersion->getId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersion:publish.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }

}
