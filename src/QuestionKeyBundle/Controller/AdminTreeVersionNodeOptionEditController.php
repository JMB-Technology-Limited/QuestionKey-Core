<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\NodeOptionVariableAction;
use QuestionKeyBundle\Form\Type\AdminNodeOptionVariableAction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminNodeOptionEditType;
use QuestionKeyBundle\Form\Type\AdminConfirmDeleteType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionNodeOptionEditController extends AdminTreeVersionNodeOptionController
{



    public function editAction($treeId, $versionId, $nodeId, $optionId)
    {

        // build
        $return = $this->build($treeId, $versionId, $nodeId, $optionId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $form = $this->createForm(new AdminNodeOptionEditType(), $this->nodeOption);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($this->nodeOption);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_option_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId(),
                    'optionId'=>$this->nodeOption->getPublicId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOptionEdit:edit.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOption'=>$this->nodeOption,
            'form' => $form->createView(),
        ));

    }


    public function deleteAction($treeId, $versionId, $nodeId, $optionId)
    {

        // build
        $return = $this->build($treeId, $versionId, $nodeId, $optionId);
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
                $doctrine->remove($this->nodeOption);
                // And all variable actions TODO
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOptionEdit:delete.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOption'=>$this->nodeOption,
            'form' => $form->createView(),
        ));

    }


    public function newVariableActionAction($treeId, $versionId, $nodeId, $optionId) {
        // build
        $return = $this->build($treeId, $versionId, $nodeId, $optionId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $nodeOptionVariableAction = new NodeOptionVariableAction();
        $nodeOptionVariableAction->setNodeOption($this->nodeOption);

        $form = $this->createForm(new AdminNodeOptionVariableAction($this->nodeOption));
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $nodeOptionVariableAction->setVariable($form->get('variable')->getData());
                $nodeOptionVariableAction->setAction($form->get('action')->getData());
                $nodeOptionVariableAction->setValue($form->get('value')->getData());
                $doctrine->persist($nodeOptionVariableAction);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_option_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$this->node->getPublicId(),
                    'optionId'=>$this->nodeOption->getPublicId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOptionEdit:newVariableAction.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOption'=>$this->nodeOption,
            'form' => $form->createView(),
        ));
    }




}
