<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class GetTreeVersionDataObjects {

    protected $container;
    /** @var TreeVersion  */
    protected $treeVersion;
    protected $isAdmin;

    function __construct($container, TreeVersion $treeVersion, $isAdmin=false) {
        $this->container = $container;
        $this->treeVersion = $treeVersion;
        $this->isAdmin = $isAdmin;
    }


    public function go() {

        $doctrine = $this->container->get('doctrine');
        $treeStartingNodeRepo = $doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $nodeRepo = $doctrine->getRepository('QuestionKeyBundle:Node');
        $nodeOptionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $nodeOptionVariableActionRepo = $doctrine->getRepository('QuestionKeyBundle:NodeOptionVariableAction');

        //data
        $out = array(
            'public_id' => $this->treeVersion->getTree()->getPublicId(),
            'nodes'=>array(),
            'nodeOptions'=>array(),
            'version' => array(
                'public_id' => $this->treeVersion->getPublicId(),
            ),
            'features'=>array(
              'library_content'=>array('status'=>false),
              'variables'=>array('status'=>false),
            ),
        );


        if ($this->treeVersion->isFeatureLibraryContent()) {
            $out['features']['library_content'] = array('status'=>true);
            $out['library_content'] = array();
            $libraryContentRepo = $doctrine->getRepository('QuestionKeyBundle:LibraryContent');
            $libraryContents = $libraryContentRepo->findByTreeVersion($this->treeVersion);
            foreach ($libraryContents as $libraryContent) {
                $out['library_content'][$libraryContent->getPublicId()] = array(
                    'id'=>$libraryContent->getPublicId(),
                    'body_html'=>$libraryContent->getBodyHTML(),
                    'body_text'=>$libraryContent->getBodyText(),
                );
            }
        }

        if ($this->treeVersion->isFeatureVariables()) {
            $out['features']['variables'] = array('status'=>true);
            $out['variables'] = array();
            $variableRepo = $doctrine->getRepository('QuestionKeyBundle:Variable');
            $variableContents = $variableRepo->findByTreeVersion($this->treeVersion);
            foreach ($variableContents as $variableContent) {
                $out['variables'][$variableContent->getName()] = array(
                    'name'=>$variableContent->getName(),
                    'type'=>$variableContent->getType(),
                );
            }
        }



        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);
        if ($treeStartingNode) {
            $out['start_node'] = array('id'=>$treeStartingNode->getNode()->getPublicId());
        }


        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion);
        foreach($nodes as $node) {
            $outNode = array(
                'id'=>$node->getPublicId(),
                'body_html'=>$node->getBodyHTML(),
                'body_text'=>$node->getBodyText(),
                'title'=>$node->getTitle(),
                'title_previous_answers'=>$node->getTitlePreviousAnswers(),
                'options'=>array(),
            );
            if ($this->isAdmin) {
                $outNode['title_admin'] = $node->getTitleAdmin();
                $outNode['url'] = $this->container->get('router')->generate("questionkey_admin_tree_version_node_show", array(
                    "treeId" => $this->treeVersion->getTree()->getPublicId(),
                    'versionId' => $this->treeVersion->getPublicId(),
                    'nodeId' => $node->getPublicId()
                ));
            }

            if ($this->treeVersion->isFeatureLibraryContent()) {
                $outNode['library_content'] = array();
                foreach ($libraryContentRepo->findForNode($node) as $libraryContent) {
                    $outNode['library_content'][$libraryContent->getPublicId()] = array(
                        'id'=>$libraryContent->getPublicId(),
                    );
                }
            }
            foreach($nodeOptionRepo->findActiveNodeOptionsForNode($node) as $nodeOption) {
                $outNode['options'][$nodeOption->getPublicId()] = array(
                    'id'=>$nodeOption->getPublicId(),
                );
            }
            $out['nodes'][$node->getPublicId()] =  $outNode;
        }

        foreach($nodeOptionRepo->findAllNodeOptionsForTreeVersion($this->treeVersion) as $nodeOption) {
            $outNodeOption =  array(
                'id'=>$nodeOption->getPublicId(),
                'title'=>$nodeOption->getTitle(),
                'body_html'=>$nodeOption->getBodyHTML(),
                'body_text'=>$nodeOption->getBodyText(),
                'node' => array(
                    'id' => $nodeOption->getNode()->getPublicId(),
                ),
                'destination_node' => array(
                    'id' => $nodeOption->getDestinationNode()->getPublicId(),
                ),
                'variableActions'=>array(),
            );
            if ($this->treeVersion->isFeatureVariables()) {
                foreach($nodeOptionVariableActionRepo->findByNodeOption($nodeOption) as $nodeOptionVariableAction) {
                    $outNodeOption['variableActions'][$nodeOptionVariableAction->getPublicId()] = array(
                        'id' => $nodeOptionVariableAction->getPublicId(),
                        'variable' => $nodeOptionVariableAction->getVariable()->getname(),
                        'action' => strtolower($nodeOptionVariableAction->getAction()),
                        'value' => $nodeOptionVariableAction->getValue(),
                    );
                }
            }
            $out['nodeOptions'][$nodeOption->getPublicId()] = $outNodeOption;
        }

        return $out;

    }


}
