<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Form\Type\AdminTreeNewImportType;
use QuestionKeyBundle\ImportExport\ImportTreeVersionJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminTreeNewType;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeNewController extends Controller
{

    public function indexAction()
    {

        $doctrine = $this->getDoctrine()->getManager();

        $tree = new Tree();
        $tree->setOwner($this->getUser());

        $form = $this->createForm(new AdminTreeNewType(), $tree);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine->persist($tree);
                $treeVersion = new TreeVersion();
                $treeVersion->setTree($tree);
                $doctrine->persist($treeVersion);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_show', array('treeId'=>$tree->getPublicId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeNew:index.html.twig', array(
            'form' => $form->createView(),
        ));


    }

    public function importAction()
    {

        $doctrine = $this->getDoctrine()->getManager();

        $tree = new Tree();
        $tree->setOwner($this->getUser());

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);

        $form = $this->createForm(new AdminTreeNewImportType(), $tree);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $importJSON = new ImportTreeVersionJSON($doctrine, $treeVersion, $form->get('data')->getData());
            if (!$importJSON->hasData()) {
                $form->addError(new FormError("That Import Data does not seem valid"));
            }
            if ($form->isValid()) {
                $doctrine->persist($tree);
                $doctrine->persist($treeVersion);
                $importJSON->process();
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_show', array('treeId'=>$tree->getPublicId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeNew:index.html.twig', array(
            'form' => $form->createView(),
        ));


    }

}
