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
class AdminTreeVersionEditController extends AdminTreeVersionController
{


    public function editAction($treeId, $versionId)
    {

        // build
        $this->build($treeId, $versionId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
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
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionEdit:edit.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }




    public function newVersionAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);

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
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_show', array('treeId'=>$this->tree->getPublicId(),'versionId'=>$newTreeVersion->getPublicId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionEdit:newVersion.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }


    public function newNodeAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
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
                $node->setTitleAdmin($form->get('titleAdmin')->getData());
                $node->setTitlePreviousAnswers($form->get('titlePreviousAnswers')->getData());
                $node->setTitle($form->get('title')->getData());
                $node->setBodyText($form->get('body_text')->getData());
                $node->setBodyHTML($form->get('body_html')->getData());
                $doctrine->persist($node);
                $doctrine->flush();


                // If this is first node on a tree version, make it the starting node now.
                $doctrine = $this->getDoctrine()->getManager();
                $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
                if ($nodeRepo->getCountNodesForTreeVersion($this->treeVersion)  == 1) {
                    $startingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
                    $startingNodeRepo->setAsStartingNode($node);
                }

                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_node_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'nodeId'=>$node->getPublicId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionEdit:newNode.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }


    public function newVariableAction($treeId, $versionId)
    {

        // build
        $this->build($treeId, $versionId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $form = $this->createForm(new AdminVariableNewType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $variable = new Variable();
                $variable->setTreeVersion($this->treeVersion);
                $variable->setName($form->get('name')->getData());
                $variable->setType($form->get('type')->getData());
                $doctrine->persist($variable);
                $doctrine->flush();

                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_variable_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'variableId'=>$variable->getName(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionEdit:newVariable.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }



    public function newLibraryContentAction($treeId, $versionId)
    {

        // build
        $this->build($treeId, $versionId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $form = $this->createForm(new AdminLibraryContentNewType());
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $libraryContent = new LibraryContent();
                $libraryContent->setTreeVersion($this->treeVersion);
                $libraryContent->setTitleAdmin($form->get('titleAdmin')->getData());
                $libraryContent->setBodyText($form->get('body_text')->getData());
                $libraryContent->setBodyHTML($form->get('body_html')->getData());
                $doctrine->persist($libraryContent);
                $doctrine->flush();

                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_library_content_show', array(
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId(),
                    'contentId'=>$libraryContent->getPublicId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionEdit:newLibraryContent.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }


    public function publishAction($treeId, $versionId)
    {

        // build
        $return = $this->build($treeId, $versionId);

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
                    'treeId'=>$this->tree->getPublicId(),
                    'versionId'=>$this->treeVersion->getPublicId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionEdit:publish.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'form' => $form->createView(),
        ));

    }

}

