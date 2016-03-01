<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="library_content", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"tree_version_id", "public_id"})})
 * @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\LibraryContentRepository")
 * @ORM\HasLifecycleCallbacks
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class LibraryContent
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
     * @ORM\Column(name="title_admin", type="string", length=250, nullable=false)
     */
    private $titleAdmin = '';


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
     * @ORM\OneToMany(targetEntity="NodeHasLibraryContent", mappedBy="libraryContent")
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

    /**
     * @return string
     */
    public function getTitleAdmin()
    {
        return $this->titleAdmin;
    }

    /**
     * @param string $titleAdmin
     */
    public function setTitleAdmin($titleAdmin)
    {
        $this->titleAdmin = $titleAdmin;
    }

    /**
     * @return string
     */
    public function getBodyText()
    {
        return $this->body_text;
    }

    /**
     * @param string $body_text
     */
    public function setBodyText($body_text)
    {
        $this->body_text = $body_text;
    }

    /**
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->body_html;
    }

    /**
     * @param string $body_html
     */
    public function setBodyHtml($body_html)
    {
        $this->body_html = $body_html;
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
     * @ORM\PrePersist()
     */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }


}
