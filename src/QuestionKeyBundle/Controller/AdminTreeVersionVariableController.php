<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Variable;
use QuestionKeyBundle\StatsDateRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminNodeEditType;
use QuestionKeyBundle\Form\Type\AdminNodeOptionNewType;
use QuestionKeyBundle\Form\Type\AdminNodeMakeStartType;
use QuestionKeyBundle\Form\Type\AdminConfirmDeleteType;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionVariableController extends Controller
{

    /** @var  Tree */
    protected $tree;
    /** @var  TreeVersion */
    protected $treeVersion;
    /** @var  boolean */
    protected $treeVersionEditable;
    /** @var  Variable */
    protected $variable;

    protected function build($treeId, $versionId, $variableId)
    {
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
            'tree' => $this->tree,
            'id' => $versionId,
        ));
        if (!$this->treeVersion) {
            throw new  NotFoundHttpException('Not found');
        }
        $this->treeVersionEditable = !$treeVersionRepo->hasEverBeenPublished($this->treeVersion);
        // load
        $variableRepo = $doctrine->getRepository('QuestionKeyBundle:Variable');
        $this->variable = $variableRepo->findOneBy(array(
            'treeVersion' => $this->treeVersion,
            'name' => $variableId,
        ));
        if (!$this->variable) {
            throw new  NotFoundHttpException('Not found');
        }
    }


    public function indexAction($treeId, $versionId, $variableId)
    {


        // build
        $this->build($treeId, $versionId, $variableId);

        //data


        return $this->render('QuestionKeyBundle:AdminTreeVersionVariable:index.html.twig', array(
            'tree' => $this->tree,
            'treeVersion' => $this->treeVersion,
            'variable' => $this->variable,

        ));


    }

}

