<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="visitor_session_ran_tree_version", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"visitor_session_id", "public_id"})})
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\VisitorSessionRanTreeVersionRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class VisitorSessionRanTreeVersion
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
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\VisitorSession")
    * @ORM\JoinColumn(name="visitor_session_id", referencedColumnName="id", nullable=false)
    */
    private $visitorSession;

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

    public function getTreeVersion()
    {
        return $this->treeVersion;
    }

    public function setTreeVersion($treeVersion)
    {
        $this->treeVersion = $treeVersion;
    }

    public function getVisitorSession()
    {
        return $this->visitorSession;
    }

    public function setVisitorSession($visitorSession)
    {
        $this->visitorSession = $visitorSession;
    }

    public function getPublicId()
    {
        return $this->publicId;
    }

    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
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
