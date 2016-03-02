<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use QuestionKeyBundle\Form\Type\AdminNodeOptionEditType;
use QuestionKeyBundle\Form\Type\AdminConfirmDeleteType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionNodeOptionController extends Controller
{


    protected $tree;
    protected $treeVersion;
    protected $treeVersionEditable;
    protected $node;
    protected $nodeOption;

    protected function build($treeId, $versionId, $nodeId, $optionId) {
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
            'tree'=>$this->tree,
            'id'=>$versionId,
        ));
        if (!$this->treeVersion) {
            throw new  NotFoundHttpException('Not found');
        }
        $this->treeVersionEditable = !$treeVersionRepo->hasEverBeenPublished($this->treeVersion);
        // load
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $this->node = $treeRepo->findOneBy(array(
            'treeVersion'=>$this->treeVersion,
            'id'=>$nodeId,
        ));
        if (!$this->node) {
            throw new  NotFoundHttpException('Not found');
        }
        // option
        $optionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $this->nodeOption = $optionRepo->findOneBy(array(
            'node'=>$this->node,
            'id'=>$optionId,
        ));
        if (!$this->nodeOption) {
            throw new  NotFoundHttpException('Not found');
        }
    }

    public function indexAction($treeId, $versionId, $nodeId, $optionId)
    {

        // build
        $return = $this->build($treeId, $versionId, $nodeId, $optionId);

        //data
        return $this->render('QuestionKeyBundle:AdminTreeVersionNodeOption:index.html.twig', array(
            'tree'=>$this->tree,
            'treeVersion'=>$this->treeVersion,
            'node'=>$this->node,
            'nodeOption'=>$this->nodeOption,
            'isTreeVersionEditable'=>$this->treeVersionEditable,
        ));

    }



}
