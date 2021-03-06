<?php

namespace QuestionKeyBundle\Controller;

use Doctrine\ORM\Mapping\Entity;

use QuestionKeyBundle\IsIPInIPConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use QuestionKeyBundle\Entity\VisitorSession;
use QuestionKeyBundle\Entity\VisitorSessionRanTreeVersion;
use QuestionKeyBundle\Entity\VisitorSessionOnNode;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class API1VisitorSessionController extends Controller
{

    protected $session;
    protected $tree;
    protected $treeVersion;
    protected $sessionRanTreeVersion;
    protected $node;
    protected $nodeOption;
    protected $goneBackTo;

    protected function getObjectsForAction(Request $request)
    {

        $doctrine = $this->getDoctrine()->getManager();
        $sessionRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSession');
        $sessionRanTreeRepo = $doctrine->getRepository('QuestionKeyBundle:VisitorSessionRanTreeVersion');
        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');
        $treeVersionRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersion');
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $nodeOptionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');

        $isIPInIPConfig = new IsIPInIPConfig($this->container->hasParameter('internal_ips') ? $this->container->getParameter('internal_ips') : '');

        // Session!
        if ($request->query->get("session_id")) {
            $this->session = $sessionRepo->findOneByPublicId($request->query->get("session_id"));
        }

        if (!$this->session) {
            $this->session = new VisitorSession();
            $this->session->setIsInternalIP($isIPInIPConfig->checkIP($request->getClientIp()));
            $doctrine->persist($this->session);
            $doctrine->flush();
            // TODO check for collisions of public ID
        }

        // Tree!
        if ($request->query->get("ran_tree_version_id")) {
            $this->sessionRanTreeVersion = $sessionRanTreeRepo->findOneByPublicId($request->query->get("ran_tree_version_id"));
            // include session to
        } else if ($request->query->get("tree_id")) {
            $this->tree = $treeRepo->findOneByPublicId($request->query->get("tree_id"));
            if ($this->tree) {
                if ($request->query->get("tree_version_id")) {
                    $this->treeVersion = $treeVersionRepo->findOneBy(array('publicId'=>$request->query->get("tree_version_id"),'tree'=>$this->tree));
                    // TODO was this version ever published?
                } else {
                    $this->treeVersion = $treeVersionRepo->findPublishedVersionForTree($this->tree);
                }
            }
        }

        if ($this->sessionRanTreeVersion && !$this->treeVersion) {
            $this->treeVersion = $this->sessionRanTreeVersion->getTreeVersion();
            $this->tree = $this->treeVersion->getTree();
        } else if ($this->treeVersion && !$this->sessionRanTreeVersion) {
            $this->sessionRanTreeVersion = new VisitorSessionRanTreeVersion();
            $this->sessionRanTreeVersion->setTreeVersion($this->treeVersion);
            $this->sessionRanTreeVersion->setVisitorSession($this->session);
            $this->sessionRanTreeVersion->setIsInternalIP($isIPInIPConfig->checkIP($request->getClientIp()));
            $doctrine->persist($this->sessionRanTreeVersion);
            $doctrine->flush();
            // TODO check for collisions of public ID
        }


        // Node!
        if ($this->treeVersion && $this->sessionRanTreeVersion && $request->query->get("node_id")) {
            $this->node = $request->query->get("node_id") ? $nodeRepo->findOneBy(array(
                'treeVersion'=>$this->treeVersion,
                'publicId'=>$request->query->get("node_id"),
            )) : null;

            if ($this->node) {
                $this->nodeOption = $request->query->get("node_option_id") ? $nodeOptionRepo->findOneBy(array(
                    'treeVersion'=>$this->treeVersion,
                    'publicId'=>$request->query->get("node_option_id"),
                )) : null;

                $sessionOnNode = new VisitorSessionOnNode();
                $sessionOnNode->setNode($this->node);
                $sessionOnNode->setNodeOption($this->nodeOption);
                $sessionOnNode->setSessionRanTreeVersion($this->sessionRanTreeVersion);
                $sessionOnNode->setGoneBackTo(filter_var( $request->query->get("gone_back_to") , FILTER_VALIDATE_BOOLEAN));
                $doctrine->persist($sessionOnNode);
                $doctrine->flush();
            }
        }

        // Out!
        $out = array(
            'session' => array(
                'id'=> $this->session->getPublicId(),
            ),
            'session_ran_tree' => null,
        );

        if ($this->sessionRanTreeVersion) {
            $out['session_ran_tree_version'] = array(
                'id'=>$this->sessionRanTreeVersion->getPublicId(),
            );
        }

        return $out;

    }

    public function actionJSONPAction(Request $request)
    {

        $data = $this->getObjectsForAction($request);

        $func  = $request->query->get('callback');
        if (!$func) {
            $func='callback';
        }

        $response = new Response($func . "(" . json_encode($data) . ")");
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;


    }

    public function actionJSONAction(Request $request)
    {

        $data = $this->getObjectsForAction($request);

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;


    }

}
