<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="node", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"tree_version_id", "public_id"})})
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\NodeRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class Node
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
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\TreeVersion")
    * @ORM\JoinColumn(name="tree_version_id", referencedColumnName="id", nullable=false)
    */
    private $treeVersion;

    /**
    * @var string
    *
    * @ORM\Column(name="title_admin", type="string", length=250, nullable=false, options={"default":""})
    */
    private $titleAdmin = '';

    /**
    * @var string
    *
    * @ORM\Column(name="title", type="text", nullable=true)
    */
    private $title;

    /**
    * @var string
    *
    * @ORM\Column(name="title_previous_answers", type="string", length=250, nullable=true)
    */
    private $titlePreviousAnswers = '';

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
    * @var datetime $createdAt
    *
    * @ORM\Column(name="created_at", type="datetime", nullable=false)
    */
    private $createdAt;

    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node")
    * @ORM\JoinColumn(name="from_old_version_id", referencedColumnName="id", nullable=true)
    */
    private $fromOldVersion;


    /**
    * @ORM\OneToMany(targetEntity="NodeOption", mappedBy="destinationNode")
    **/
    private $nodeOptionsDestination;

    /**
    * @ORM\OneToMany(targetEntity="NodeOption", mappedBy="node")
    **/
    private $nodeOptionsSource;


    /**
     * @ORM\OneToMany(targetEntity="NodeHasLibraryContent", mappedBy="node")
     **/
    private $hasLibraryContents;


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

    public function getTreeVersion()
    {
        return $this->treeVersion;
    }

    public function setTreeVersion($treeVersion)
    {
        $this->treeVersion = $treeVersion;
    }

    public function getTitleAdmin()
    {
        return $this->titleAdmin ? $this->titleAdmin : $this->title;
    }

    public function setTitleAdmin($title)
    {
        $this->titleAdmin = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * Get the value of Title Previous Answers
     *
     * @return string
     */
    public function getTitlePreviousAnswers()
    {
        return $this->titlePreviousAnswers;
    }

    /**
     * Set the value of Title Previous Answers
     *
     * @param string titlePreviousAnswers
     *
     * @return self
     */
    public function setTitlePreviousAnswers($titlePreviousAnswers)
    {
        $this->titlePreviousAnswers = $titlePreviousAnswers;

        return $this;
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

    public function getNodeOptionsDestination() {
        return $this->getNodeOptionsDestination;
    }

    public function getNodeOptionsSource() {
        return $this->getNodeOptionsSource;
    }

    /**
    * @ORM\PrePersist()
    */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }

    public function __toString () {
        return ( $this->titleAdmin ? $this->titleAdmin : $this->title ) . " (ID: ".$this->publicId.")";
    }






}
