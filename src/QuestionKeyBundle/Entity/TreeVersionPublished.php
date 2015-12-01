<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="tree_version_published", uniqueConstraints={@ORM\UniqueConstraint(name="version_published_at", columns={"tree_version_id", "published_at"})})
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\TreeVersionPublishedRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class TreeVersionPublished
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
    *
    * @ORM\Column(name="published_at", type="datetime", nullable=false)
    */
    private $publishedAt;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\User")
    * @ORM\JoinColumn(name="published_by_id", referencedColumnName="id", nullable=true)
    */
    private $publishedBy;

    /**
    * @var string
    *
    * @ORM\Column(name="comment_published_admin", type="text", nullable=true)
    */
    private $commentPublishedAdmin;

    /**
    * Get the value of Id
    *
    * @return integer
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * Set the value of Id
    *
    * @param integer id
    *
    * @return self
    */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
    * Get the value of Published At
    *
    * @return mixed
    */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
    * Set the value of Published At
    *
    * @param mixed publishedAt
    *
    * @return self
    */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
    * Get the value of Published By
    *
    * @return mixed
    */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
    * Set the value of Published By
    *
    * @param mixed publishedBy
    *
    * @return self
    */
    public function setPublishedBy($publishedBy)
    {
        $this->publishedBy = $publishedBy;

        return $this;
    }

    /**
    * Get the value of Tree Version
    *
    * @return mixed
    */
    public function getTreeVersion()
    {
        return $this->treeVersion;
    }

    /**
    * Set the value of Tree Version
    *
    * @param mixed treeVersion
    *
    * @return self
    */
    public function setTreeVersion($treeVersion)
    {
        $this->treeVersion = $treeVersion;

        return $this;
    }


    /**
    * Get the value of Comment Published Admin
    *
    * @return string
    */
    public function getCommentPublishedAdmin()
    {
        return $this->commentPublishedAdmin;
    }

    /**
    * Set the value of Comment Published Admin
    *
    * @param string commentPublishedAdmin
    *
    * @return self
    */
    public function setCommentPublishedAdmin($commentPublishedAdmin)
    {
        $this->commentPublishedAdmin = $commentPublishedAdmin;

        return $this;
    }


    /**
    * @ORM\PrePersist()
    */
    public function beforeFirstSave() {
        $this->publishedAt = new \DateTime("", new \DateTimeZone("UTC"));
    }




}
