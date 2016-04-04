<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="node_has_library_content_if_variable"), uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"node_id", "public_id"})}
 * @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\NodeHasLibraryContentIfVariableRepository")
 * @ORM\HasLifecycleCallbacks
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeHasLibraryContentIfVariable
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
     * @ORM\Column(name="public_id", type="string", length=250, nullable=false)
     * @Assert\NotBlank()
     */
    private $publicId;

    /**
     *
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node", inversedBy="hasLibraryContents")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", nullable=false)
     */
    private $node;

    /**
     *
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\LibraryContent", inversedBy="hasLibraryContents")
     * @ORM\JoinColumn(name="library_content_id", referencedColumnName="id", nullable=false)
     */
    private $libraryContent;

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
    private $action = "==";

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\NodeHasLibraryContentIfVariable")
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
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @return mixed
     */
    public function getLibraryContent()
    {
        return $this->libraryContent;
    }

    /**
     * @param mixed $libraryContent
     */
    public function setLibraryContent($libraryContent)
    {
        $this->libraryContent = $libraryContent;
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