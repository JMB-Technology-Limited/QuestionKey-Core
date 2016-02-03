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
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\NodeOption")
    * @ORM\JoinColumn(name="node_option_id", referencedColumnName="id", nullable=true)
    */
    private $nodeOption = null;

    /**
    * @ORM\Column(name="gone_back_to", type="boolean", nullable=false, options={"default":"0"})
    */
    private $goneBackTo = false;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\VisitorSessionRanTreeVersion", inversedBy="onNodes")
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


        /**
         * Get the value of Node Option
         *
         * @return mixed
         */
        public function getNodeOption()
        {
            return $this->nodeOption;
        }

        /**
         * Set the value of Node Option
         *
         * @param mixed nodeOption
         *
         * @return self
         */
        public function setNodeOption($nodeOption)
        {
            $this->nodeOption = $nodeOption;

            return $this;
        }

        /**
         * Get the value of Gone Back To
         *
         * @return mixed
         */
        public function getGoneBackTo()
        {
            return $this->goneBackTo;
        }

        /**
         * Set the value of Gone Back To
         *
         * @param mixed goneBackTo
         *
         * @return self
         */
        public function setGoneBackTo($goneBackTo)
        {
            $this->goneBackTo = $goneBackTo;

            return $this;
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
