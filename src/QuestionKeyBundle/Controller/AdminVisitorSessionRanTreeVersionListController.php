<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\Repository\Builder\VisitorSessionRanTreeVersionRepositoryBuilder;
use QuestionKeyBundle\Entity\Repository\Builder\VisitorSessionRepositoryBuilder;
use QuestionKeyBundle\StatsDateRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminVisitorSessionRanTreeVersionListController extends Controller
{

    public function indexAction(Request $request)
    {

        $dateRange = new StatsDateRange();
        $dateRange->setFromRequest($request);

        $repositoryBuilder = new VisitorSessionRanTreeVersionRepositoryBuilder();
        $repositoryBuilder->setDateRange($dateRange);

        $doctrine = $this->getDoctrine()->getManager();
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');

        $sessions = $treeRepo->findByBuilder($repositoryBuilder);

        return $this->render('QuestionKeyBundle:AdminVisitorSessionRanTreeVersionList:index.html.twig', array(
            'dateRange'=>$dateRange,
            'sessionRanTreeVersions'=>$sessions,
        ));

    }

}
