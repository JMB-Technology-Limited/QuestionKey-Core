<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
                return $this->redirect($this->generateUrl('questionkey_admin_tree_show', array('treeId'=>$tree->getId())));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTreeNew:index.html.twig', array(
            'form' => $form->createView(),
        ));


    }

}
