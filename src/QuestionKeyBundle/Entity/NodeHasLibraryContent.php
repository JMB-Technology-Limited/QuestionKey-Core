<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="node_has_library_content")})
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class NodeHasLibraryContent
{

    /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Node")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", nullable=false)
     */
    private $node;

    /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\LibraryContent", inversedBy="hasLibraryContents")
     * @ORM\JoinColumn(name="library_content_id", referencedColumnName="id", nullable=false)
     */
    private $libraryContent;


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
     * @ORM\PrePersist()
     */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
    }

}

