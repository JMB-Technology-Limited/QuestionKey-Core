<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="node_option", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"tree_version_id", "public_id"})})
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\NodeOptionRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class NodeOption
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
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\TreeVersion")
    * @ORM\JoinColumn(name="tree_version_id", referencedColumnName="id", nullable=false)
    */
    private $treeVersion;

    /**
    * @ORM\Column(name="public_id", type="string", length=250, nullable=false)
    * @Assert\NotBlank()
    */
    private $publicId;

    /**
    * This is the source node
    *
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node", inversedBy="nodeOptionsSource")
    * @ORM\JoinColumn(name="node_id", referencedColumnName="id", nullable=false)
    */
    private $node;


    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node", inversedBy="nodeOptionsDestination")
    * @ORM\JoinColumn(name="destination_node_id", referencedColumnName="id", nullable=true)
    */
    private $destinationNode;

    /**
    * @var string
    *
    * @ORM\Column(name="title", type="text", nullable=true)
    */
    private $title;

    /**
    * @var string
    *
    * @ORM\Column(name="body_text", type="text", nullable=true)
    */
    private $body_text;

    /**
    * @var string
    *
    * @ORM\Column(name="body_html", type="text", nullable=true)
    */
    private $body_html;

    /**
    * @var integer
    *
    * @ORM\Column(name="sort", type="integer", nullable=true)
    */
    private $sort;

    /**
    * @var datetime $createdAt
    *
    * @ORM\Column(name="created_at", type="datetime", nullable=false)
    */
    private $createdAt;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\NodeOption")
    * @ORM\JoinColumn(name="from_old_version_id", referencedColumnName="id", nullable=true)
    */
    private $fromOldVersion;



    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTreeVersion()
    {
        return $this->treeVersion;
    }

    public function setTreeVersion($treeVersion)
    {
        $this->treeVersion = $treeVersion;
    }

    public function getPublicId()
    {
        return $this->publicId;
    }

    public function setPublicId($id)
    {
        $this->publicId = $id;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function setNode($node)
    {
        $this->node = $node;
    }

    public function getDestinationNode()
    {
        return $this->destinationNode;
    }

    public function setDestinationNode($destinationnode)
    {
        $this->destinationNode = $destinationnode;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getBodyText()
    {
        return $this->body_text;
    }

    public function setBodyText($body_text)
    {
        $this->body_text = $body_text;
    }

    public function getBodyHTML()
    {
        return $this->body_html;
    }

    public function setBodyHTML($body_html)
    {
        $this->body_html = $body_html;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }



    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getFromOldVersion()
    {
        return $this->fromOldVersion;
    }

    public function setFromOldVersion($fromOldVersion)
    {
        $this->fromOldVersion = $fromOldVersion;
    }



    /**
    * @ORM\PrePersist()
    */
    public function beforeFirstSave() {
        if (!$this->publicId) {
            $this->publicId = \QuestionKeyBundle\QuestionKeyBundle::createKey(1,250);
        }
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }




}
