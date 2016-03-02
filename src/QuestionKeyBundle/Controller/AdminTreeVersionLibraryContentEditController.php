<?php

namespace QuestionKeyBundle\Controller;


use QuestionKeyBundle\Form\Type\AdminLibraryContentEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionLibraryContentEditController extends AdminTreeVersionLibraryContentController
{

    public function editAction($treeId, $versionId, $contentId, Request $request)
    {

        // build
        $this->build($treeId, $versionId, $contentId);
        if (!$this->treeVersionEditable) {
            throw new AccessDeniedException();
        }

        //data
        $form = $this->createForm(new AdminLibraryContentEditType(), $this->libraryContent);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($this->libraryContent);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_version_library_content_show', array(
                    'treeId'=>$this->tree->getId(),
                    'versionId'=>$this->treeVersion->getId(),
                    'contentId'=>$this->libraryContent->getPublicId(),
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeVersionLibraryContentEdit:edit.html.twig', array(
            'tree' => $this->tree,
            'treeVersion' => $this->treeVersion,
            'libraryContent' => $this->libraryContent,
            'form' => $form->createView(),
        ));

    }



}