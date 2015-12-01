<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="visitor_session_on_node")
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\VisitorSessionOnNodeRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class VisitorSessionOnNode
{

    /**
    * @var integer
    *
    * @ORM\Column(name="id", type="bigint", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node")
    * @ORM\JoinColumn(name="node_id", referencedColumnName="id", nullable=false)
    */
    private $node;


    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\VisitorSessionRanTreeVersion")
    * @ORM\JoinColumn(name="session_ran_tree_version_id", referencedColumnName="id", nullable=false)
    */
    private $sessionRanTreeVersion;

    /**
    * @var datetime $createdAt
    *
    * @ORM\Column(name="created_at", type="datetime", nullable=false)
    */
    private $createdAt;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getNode()
    {
        return $this->node;
    }

    public function setNode($node)
    {
        $this->node = $node;
    }

    public function getSessionRanTreeVersion()
    {
        return $this->sessionRanTreeVersion;
    }

    public function setSessionRanTreeVersion($sessionRanTreeVersion)
    {
        $this->sessionRanTreeVersion = $sessionRanTreeVersion;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
    * @ORM\PrePersist()
    */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }


}
