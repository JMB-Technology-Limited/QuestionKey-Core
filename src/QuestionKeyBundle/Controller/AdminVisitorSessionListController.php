<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\Repository\Builder\VisitorSessionRepositoryBuilder;
use QuestionKeyBundle\StatsDateRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminVisitorSessionListController extends Controller
{

    public function indexAction(Request $request)
    {

        $dateRange = new StatsDateRange();
        $dateRange->setFromRequest($request);

        $repositoryBuilder = new VisitorSessionRepositoryBuilder();
        $repositoryBuilder->setDateRange($dateRange);

        $doctrine = $this->getDoctrine()->getManager();
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSession');

        $sessions = $treeRepo->findByBuilder($repositoryBuilder);

        return $this->render('QuestionKeyBundle:AdminVisitorSessionList:index.html.twig', array(
            'dateRange'=>$dateRange,
            'sessions'=>$sessions,
        ));

    }

}
