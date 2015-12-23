<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="tree_version_preview_code", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"tree_version_id", "code"})})
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\TreeVersionPreviewCodeRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class TreeVersionPreviewCode
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
    * @ORM\Column(name="code", type="string", length=250, nullable=false)
    * @Assert\NotBlank()
    */
    private $code;

    /**
    * @var datetime $createdAt
    *
    * @ORM\Column(name="created_at", type="datetime", nullable=false)
    */
    private $createdAt;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\User")
    * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=false)
    */
    private $createdBy;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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


    public function getCode()
    {
        return $this->code;
    }

    public function setCode($id)
    {
        $this->code = $id;
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
     * Get the value of Created By
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the value of Created By
     *
     * @param mixed createdBy
     *
     * @return self
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
    * @ORM\PrePersist()
    */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }

}
