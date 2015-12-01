<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="tree")
* @ORM\Entity()
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class Tree
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
    * @ORM\Column(name="public_id", type="string", length=250, unique=true, nullable=false)
    * @Assert\NotBlank()
    */
    private $publicId;

    /**
    * @var string
    *
    * @ORM\Column(name="title_admin", type="string", length=250, nullable=false)
    */
    private $titleAdmin;


    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\User")
    * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
    */
    private $owner;

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

    public function getPublicId()
    {
        return $this->publicId;
    }

    public function setPublicId($id)
    {
        $this->publicId = $id;
    }

    public function getTitleAdmin()
    {
        return $this->titleAdmin;
    }

    public function setTitleAdmin($title)
    {
        $this->titleAdmin = $title;
    }


    /**
    * Get the value of Owner
    *
    * @return mixed
    */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
    * Set the value of Owner
    *
    * @param mixed owner
    *
    * @return self
    */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
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
        if (!$this->publicId) {
            $this->publicId = \QuestionKeyBundle\QuestionKeyBundle::createKey(10,100);
        }
    }




}
