<?php

namespace QuestionKeyBundle\ImportExport;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\TreeVersion;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class ExportTreeVersionJSON
{

    protected $doctrine;
    protected $treeVersion;

    function __construct($doctrine, TreeVersion $version) {
        $this->doctrine = $doctrine;
        $this->treeVersion = $version;
    }

    function getAsText() {

        //data
        $out = array(
            'nodes'=>array(),
            'nodeOptions'=>array(),
        );

        $treeStartingNodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->treeVersion);
        if ($treeStartingNode) {
            $out['start_node'] = array('id'=>$treeStartingNode->getNode()->getPublicId());
        }

        $nodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:Node');
        $nodeOptionRepo = $this->doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $nodes = $nodeRepo->findByTreeVersion($this->treeVersion);
        foreach($nodes as $node) {
            $outNode = array(
                'id'=>$node->getPublicId(),
                'body_html'=>$node->getBodyHTML(),
                'body_text'=>$node->getBodyText(),
                'title_admin' => $node->getTitleAdmin(),
                'title'=>$node->getTitle(),
                'title_previous_answers'=>$node->getTitlePreviousAnswers(),
                'options'=>array(),
            );
            foreach($nodeOptionRepo->findActiveNodeOptionsForNode($node) as $nodeOption) {
                $destNode = $nodeOption->getDestinationNode();
                $outNode['options'][$nodeOption->getPublicId()] = array(
                    'id'=>$nodeOption->getPublicId(),
                );
            }
            $out['nodes'][$node->getPublicId()] =  $outNode;
        }

        foreach($nodeOptionRepo->findAllNodeOptionsForTreeVersion($this->treeVersion) as $nodeOption) {
            $out['nodeOptions'][$nodeOption->getPublicId()] = array(
                'id'=>$nodeOption->getPublicId(),
                'title'=>$nodeOption->getTitle(),
                'body_html'=>$nodeOption->getBodyHTML(),
                'body_text'=>$nodeOption->getBodyText(),
                'sort'=>$nodeOption->getSort(),
                'node' => array(
                    'id' => $nodeOption->getNode()->getPublicId(),
                ),
                'destination_node' => array(
                    'id' => $nodeOption->getDestinationNode()->getPublicId(),
                ),
            );
        }

        return json_encode($out, JSON_PRETTY_PRINT);


    }

}
