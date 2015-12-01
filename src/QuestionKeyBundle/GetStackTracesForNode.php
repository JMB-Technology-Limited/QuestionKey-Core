<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class GetStackTracesForNode {

    protected $doctrine;
    protected $node;

    protected $startNode;

    protected $stacktraces = array();

    function __construct($doctrine, Node $node) {
        $this->doctrine = $doctrine;
        $this->node = $node;
    }

    public function go() {

        $treeStartingNodeRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        $this->treeStartingNode = $treeStartingNodeRepo->findOneByTreeVersion($this->node->getTreeVersion());

        if (!$this->treeStartingNode) {
            return false;
        }

        $nodeOptionRepo = $this->doctrine->getRepository('QuestionKeyBundle:NodeOption');
        $stackTracesToProcess = array(
            new StackTraceForNode($this->node),
        );

        while ($stackTraceToProcess = array_pop($stackTracesToProcess)) {


            $node = $stackTraceToProcess->getCurrentNode();

            foreach($nodeOptionRepo->findActiveIncomingNodeOptionsForNode($node) as $nodeOption) {


                if (!$stackTraceToProcess->isNodeOptionAlreadyKnown($nodeOption)) {

                    $newNode = $nodeOption->getNode();
                    $newStackTrace = $stackTraceToProcess->getStackTraceWithNewCurrent($nodeOption, $newNode);

                    if ($newNode->getId() == $this->treeStartingNode->getNode()->getId()) {
                        $this->stacktraces[] = $newStackTrace;
                    } else {
                        array_push($stackTracesToProcess, $newStackTrace);
                    }

                }

            }

        }

        usort($this->stacktraces, 'QuestionKeyBundle\GetStackTracesForNode::sortStackTraces');

    }

    public function getStackTraces() {
        return $this->stacktraces;
    }

    public static function sortStackTraces(StackTraceForNode $a, StackTraceForNode $b) {
        if ($a->getPublicId() == $b->getPublicId()) {
            return 0;
        }
        return ($a->getPublicId() < $b->getPublicId()) ? -1 : 1;
    }

}
