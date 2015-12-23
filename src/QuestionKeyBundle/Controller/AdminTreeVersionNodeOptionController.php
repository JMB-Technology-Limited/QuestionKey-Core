<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminNodeOptionEditType;
use QuestionKeyBundle\Form\Type\AdminConfirmDeleteType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionNodeOptionController extends Controller
{


    protected $tree;
    protected $treeVersion;
    protected $node;
    protected $nodeOption;

    protected function build($treeId, $versionId, $nodeId, $optionId) {
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
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $this->node = $treeRepo->findOneBy(array(
            'treeVersion'=>$this->treeVersion,
            'id'=>$nodeId,
        ));
        if (!$this->node) {
            throw new  NotFoundHttpException('Not found');
        }
        // option
        $optionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $this->nodeOption = $optionRepo->findOneBy(array(
            'node'=>$this->node,
            'id'=>$optionId,
        ));
        if (!$this->nodeOption) {
            throw new  NotFoundHttpException('Not found');
        }
    }

    public function indexAction($treeId, $versionId, $nodeId, $optionId)
    {

        // build
        $return = $this->build($treeId, $versionId, $nodeId, $optionId);

        //data
        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOption:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOption'=>$this->nodeOption,
        ));

    }

    public function editAction($treeId, $versionId, $nodeId, $optionId)
    {

        // build
        $return = $this->build($treeId, $versionId, $nodeId, $optionId);

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
                    'treeId'=>$this->tree->getId(),
                    'versionId'=>$this->treeVersion->getId(),
                    'nodeId'=>$this->node->getId(),
                    'optionId'=>$this->nodeOption->getId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOption:edit.html.twig', array(
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

        //data
        $form = $this->createForm(new AdminConfirmDeleteType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->remove($this->nodeOption);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getId(),
                    'versionId'=>$this->treeVersion->getId(),
                    'nodeId'=>$this->node->getId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOption:delete.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOption'=>$this->nodeOption,
            'form' => $form->createView(),
        ));

    }




}
