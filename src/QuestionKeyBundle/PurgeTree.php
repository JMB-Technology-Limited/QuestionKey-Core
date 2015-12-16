<?php

namespace QuestionKeyBundle;

use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class PurgeTree {

    protected $doctrine;
    protected $tree;

    function __construct($doctrine, Tree $tree) {
        $this->doctrine = $doctrine;
        $this->tree = $tree;
    }

    public function go() {

        $tvRepo = $this->doctrine->getRepository('QuestionKeyBundle:TreeVersion');

        foreach($tvRepo->findByTree($this->tree) as $treeVersion) {

            $purgeTreeVersion = new PurgeTreeVersion($this->doctrine, $treeVersion);
            $purgeTreeVersion->go();

        }

        $this->doctrine->remove($this->tree);
        $this->doctrine->flush($this->tree);

    }

}
