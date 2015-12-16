<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class PurgeTreeVersion {

    protected $doctrine;
    protected $treeVersion;

    function __construct($doctrine, TreeVersion $treeVersion) {
        $this->doctrine = $doctrine;
        $this->treeVersion = $treeVersion;
    }

    public function go() {

        $records = array();

        // Published Records
        $pRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersionPublished');
        foreach($pRepo->findByTreeVersion($this->treeVersion) as $published) {
            $records[] = $published;
        }

        // Starting Node
        $snRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersionStartingNode');
        foreach($snRepo->findByTreeVersion($this->treeVersion) as $startingNodeOption) {
            $records[] = $startingNodeOption;
        }

        // Visitor Sessions
        // TODO

        // Node Options
        $noRepo = $this->doctrine->getRepository('QuestionKeyBundle:NodeOption');
        foreach($noRepo->findByTreeVersion($this->treeVersion) as $nodeOption) {
            $records[] = $nodeOption;
        }

        // Node
        $nRepo = $this->doctrine->getRepository('QuestionKeyBundle:Node');
        foreach($nRepo->findByTreeVersion($this->treeVersion) as $node) {
            $records[] = $node;
        }

        $records[] = $this->treeVersion;

        // GO GO GO
        foreach($records as $record) {
            $this->doctrine->remove($record);
        }
        $this->doctrine->flush($records);


    }

}
