<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionListController extends Controller
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

        $tvRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $tvs = $tvRepo->findByTree($this->tree, array('createdAt'=>'ASC'));

        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $treeVersionPublished = $treeVersionRepo->findPublishedVersionForTree($this->tree);

        return $this->render('QuestionKeyBundle:AdminTreeVersionList:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersions'=>$tvs,
            'treeVersionPublished' => $treeVersionPublished,
        ));


    }



}
