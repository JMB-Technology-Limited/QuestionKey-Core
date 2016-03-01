<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\StatsDateRange;
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



    public function statsAction($treeId)
    {


        // build
        $return = $this->build($treeId);

        //data
        $statsDateRange = new StatsDateRange();


        $doctrine = $this->getDoctrine()->getManager();

        $tsrtvRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');

        return $this->render('QuestionKeyBundle:AdminTree:stats.html.twig', array(
            'tree'=>$this->tree,
            'dateRange'=>$statsDateRange,
            'countTimesRan'=>$tsrtvRepo->getStatsCountTimesRanForTree($this->tree, $statsDateRange),
        ));


    }

}
