<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="tree_version_starting_node")
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\TreeVersionStartingNodeRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class TreeVersionStartingNode
{

    /**
    * @ORM\Id
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\TreeVersion")
    * @ORM\JoinColumn(name="tree_version_id", referencedColumnName="id", nullable=false)
    */
    private $treeVersion;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node")
    * @ORM\JoinColumn(name="node_id", referencedColumnName="id", nullable=false)
    */
    private $node;

    public function getTreeVersion()
    {
        return $this->treeVersion;
    }

    public function setTreeVersion($tree)
    {
        $this->treeVersion = $tree;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function setNode($node)
    {
        $this->node = $node;
    }

}
