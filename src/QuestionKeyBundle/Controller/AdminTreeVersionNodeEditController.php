<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\NodeHasLibraryContent;
use QuestionKeyBundle\Entity\NodeHasLibraryContentIfVariable;
use QuestionKeyBundle\Form\Type\AdminNodeEditAddLibraryContentIfVariable;
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
class AdminTreeVersionNodeEditController extends AdminTreeVersionNodeController
{





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
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:edit.html.twig', array(
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
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:delete.html.twig', array(
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
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:makeStart.html.twig', array(
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
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:newOption.html.twig', array(
            'form' => $form->createView(),
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
        ));


    }


    public function addLibraryContentAction($treeId, $versionId, $nodeId, Request $request)
    {

        $doctrine = $this->getDoctrine()->getManager();
        $libraryContentRepo = $doctrine->getRepository('QuestionKeyBundle:LibraryContent');

        // build
        $return = $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data

        if ($request->getMethod() == 'POST' && $request->request->get('action') == 'add') {
            $content = $libraryContentRepo->findOneBy(array('treeVersion'=>$this->treeVersion, 'publicId'=>$request->request->get('contentId')));
            if ($content) {

                $doctrine->getRepository('QuestionKeyBundle:NodeHasLibraryContent')->addLibraryContentToNode($content, $this->node);

                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId())));

            }
        }


        $contents = $libraryContentRepo->findByTreeVersion($this->treeVersion);

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:addLibraryContent.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'libraryContents'=>$contents,
        ));


    }


    public function editLibraryContentAction($treeId, $versionId, $nodeId, Request $request)
    {

        $doctrine = $this->getDoctrine()->getManager();
        $libraryContentRepo = $doctrine->getRepository('QuestionKeyBundle:LibraryContent');
        $nodeHasLibraryContentIfVariableRepo = $doctrine->getRepository('QuestionKeyBundle:NodeHasLibraryContentIfVariable');

        // build
        $return = $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data



        if ($request->getMethod() == 'POST' && $request->request->get('action') == 'remove') {
            $content = $libraryContentRepo->findOneBy(array('treeVersion'=>$this->treeVersion, 'publicId'=>$request->request->get('contentId')));
            if ($content) {
                $doctrine->getRepository('QuestionKeyBundle:NodeHasLibraryContent')->removeLibraryContentFromNode($content, $this->node);
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId())));
            }
        }


        if ($request->getMethod() == 'POST' && $request->request->get('action') == 'removeIfVariables') {
            $nodeHasLibraryContentIfVariable = $nodeHasLibraryContentIfVariableRepo->findOneBy(array('node'=>$this->node, 'publicId'=>$request->request->get('ifVariableId')));
            if ($nodeHasLibraryContentIfVariable) {
                $doctrine->remove($nodeHasLibraryContentIfVariable);
                $doctrine->flush($nodeHasLibraryContentIfVariable);
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_library_content_edit', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId())));
            }
        }


        $contents = $libraryContentRepo->findForNode($this->node);

        $nodeHasLibraryContentIfVariables = null;
        if ($this->treeVersion->isFeatureVariables()) {
            $nodeHasLibraryContentIfVariableRepo = $doctrine->getRepository('QuestionKeyBundle:NodeHasLibraryContentIfVariable');
            $nodeHasLibraryContentIfVariables = $nodeHasLibraryContentIfVariableRepo->findBy(array('node' => $this->node));

        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:editLibraryContent.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeHasLibraryContentIfVariables' => $nodeHasLibraryContentIfVariables,
            'libraryContents'=>$contents,
        ));


    }

    public function libraryContentAddIfVariableAction($treeId, $versionId, $nodeId, $libraryContentId, Request $request) {

        $this->build($treeId, $versionId, $nodeId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        $doctrine = $this->getDoctrine()->getManager();
        $libraryContentRepo = $doctrine->getRepository('QuestionKeyBundle:LibraryContent');
        $libraryContent = $libraryContentRepo->findOneBy(array('publicId'=>$libraryContentId, 'treeVersion'=>$this->treeVersion));

        if (!$libraryContent) {
            throw new  NotFoundHttpException('Not found');
        }

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $form = $this->createForm(new AdminNodeEditAddLibraryContentIfVariable($this->node));
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $nodeHasLibraryContentIfVariable = new NodeHasLibraryContentIfVariable();
                $nodeHasLibraryContentIfVariable->setNode($this->node);
                $nodeHasLibraryContentIfVariable->setLibraryContent($libraryContent);
                $nodeHasLibraryContentIfVariable->setVariable($form->get('variable')->getData());
                $nodeHasLibraryContentIfVariable->setAction($form->get('action')->getData());
                $nodeHasLibraryContentIfVariable->setValue($form->get('value')->getData());
                $doctrine->persist($nodeHasLibraryContentIfVariable);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_library_content_edit', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId()
                )));
            }
        }



        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeEdit:libraryContentAddIfVariable.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'libraryContent'=>$libraryContent,
            'form' => $form->createView(),
        ));

    }


}
