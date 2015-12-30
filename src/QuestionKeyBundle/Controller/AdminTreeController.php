<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminTreeEditType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeController extends Controller
{


    protected $tree;

    protected function build($treeId) {
        $doctrine = $this->getDoctrine()->getManager();
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $this->tree = $treeRepo->findOneById($treeId);
        if (!$this->tree) {
            throw new  NotFoundHttpException('Not found');
        }
    }



    public function indexAction($treeId)
    {


        // build
        $return = $this->build($treeId);

        //data
        $doctrine = $this->getDoctrine()->getManager();
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');

        return $this->render('QuestionKeyBundle:AdminTree:index.html.twig', array(
            'tree'=>$this->tree,
            'publishedTreeVersion'=>$treeVersionRepo->findPublishedVersionForTree($this->tree),
            'latestTreeVersion'=>$treeVersionRepo->findLatestVersionForTree($this->tree),
        ));


    }


    public function editAction($treeId)
    {


        // build
        $return = $this->build($treeId);

        //data
        $form = $this->createForm(new AdminTreeEditType(), $this->tree);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($this->tree);
                $doctrine->flush();
                return $this->redirect($this->generateUrl('questionkey_admin_tree_show', array(
                    'treeId'=>$this->tree->getId()
                )));
            }
        }

        return $this->render('QuestionKeyBundle:AdminTree:edit.html.twig', array(
            'form' => $form->createView(),
            'tree'=>$this->tree,
        ));


    }


}
