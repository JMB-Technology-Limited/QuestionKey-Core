<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="node_option_variable_action", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"node_option_id", "public_id"})})
 * @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\NodeOptionVariableActionRepository")
 * @ORM\HasLifecycleCallbacks
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeOptionVariableAction
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
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\NodeOption")
     * @ORM\JoinColumn(name="node_option_id", referencedColumnName="id", nullable=false)
     */
    private $nodeOption;

    /**
     * @ORM\Column(name="public_id", type="string", length=250, nullable=false)
     * @Assert\NotBlank()
     */
    private $publicId;

    /**
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Variable")
     * @ORM\JoinColumn(name="variable_id", referencedColumnName="id", nullable=false)
     */
    private $variable;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=250, nullable=false)
     */
    private $action = "assign";

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=false)
     */
    private $sort = 0;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\NodeOptionVariableAction")
     * @ORM\JoinColumn(name="from_old_version_id", referencedColumnName="id", nullable=true)
     */
    private $fromOldVersion;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNodeOption()
    {
        return $this->nodeOption;
    }

    /**
     * @param mixed $nodeOption
     */
    public function setNodeOption($nodeOption)
    {
        $this->nodeOption = $nodeOption;
    }

    /**
     * @return mixed
     */
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * @param mixed $publicId
     */
    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
    }

    /**
     * @return mixed
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param mixed $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getFromOldVersion()
    {
        return $this->fromOldVersion;
    }

    /**
     * @param mixed $fromOldVersion
     */
    public function setFromOldVersion($fromOldVersion)
    {
        $this->fromOldVersion = $fromOldVersion;
    }


    /**
     * @ORM\PrePersist()
     */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }

}

