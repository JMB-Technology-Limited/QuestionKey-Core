<?php

namespace QuestionKeyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="tree_version", uniqueConstraints={@ORM\UniqueConstraint(name="public_id", columns={"tree_id", "public_id"}),@ORM\UniqueConstraint(name="title_admin", columns={"tree_id", "title_admin"})})
* @ORM\Entity(repositoryClass="QuestionKeyBundle\Entity\TreeVersionRepository")
* @ORM\HasLifecycleCallbacks
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class TreeVersion
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
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\Tree")
    * @ORM\JoinColumn(name="tree_id", referencedColumnName="id", nullable=false)
    */
    private $tree;


    /**
    * @ORM\ManyToOne(targetEntity="QuestionKeyBundle\Entity\TreeVersion")
    * @ORM\JoinColumn(name="from_old_version_id", referencedColumnName="id", nullable=true)
    */
    private $fromOldVersion;

    /**
    * @var string
    *
    * @ORM\Column(name="title_admin", type="string", length=250, nullable=false)
    */
    private $titleAdmin;


    /**
    * @var datetime $createdAt
    *
    * @ORM\Column(name="created_at", type="datetime", nullable=false)
    */
    private $createdAt;

    /**
    * @var string
    *
    * @ORM\Column(name="graph_layout", type="text", nullable=true)
    */
    private $graphLayout;


    /**
     * @var boolean
     * Should be nullable=false but there is old data. We can treat null as false.
     * @ORM\Column(name="feature_variables", type="boolean", nullable=true)
     */
    private $featureVariables = false;

    /**
     * @var boolean
     * Should be nullable=false but there is old data. We can treat null as false.
     * @ORM\Column(name="feature_library_content", type="boolean", nullable=true)
     */
    private $featureLibraryContent = false;

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

    public function getTree()
    {
        return $this->tree;
    }

    public function setTree($tree)
    {
        $this->tree = $tree;
    }

    public function getTitleAdmin()
    {
        return $this->titleAdmin;
    }

    public function setTitleAdmin($title)
    {
        $this->titleAdmin = $title;
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

    public function setFromOldVersion(TreeVersion $fromOldVersion)
    {
        $this->fromOldVersion = $fromOldVersion;
        $this->featureVariables = $fromOldVersion->isFeatureVariables();
        $this->featureLibraryContent = $fromOldVersion->isFeatureLibraryContent();
        $this->graphLayout = $fromOldVersion->getGraphLayout();
    }

    /**
     * @return boolean
     */
    public function isFeatureVariables()
    {
        return $this->featureVariables;
    }

    /**
     * @param boolean $featureVariables
     */
    public function setFeatureVariables($featureVariables)
    {
        $this->featureVariables = $featureVariables;
    }

    /**
     * @return boolean
     */
    public function isFeatureLibraryContent()
    {
        return $this->featureLibraryContent;
    }

    /**
     * @param boolean $featureLibraryContent
     */
    public function setFeatureLibraryContent($featureLibraryContent)
    {
        $this->featureLibraryContent = $featureLibraryContent;
    }



    /**
     * Get the value of Graph Layout
     *
     * @return string
     */
    public function getGraphLayout()
    {
        return $this->graphLayout;
    }

    /**
     * Set the value of Graph Layout
     *
     * @param string graphLayout
     *
     * @return self
     */
    public function setGraphLayout($graphLayout)
    {
        $this->graphLayout = $graphLayout;

        return $this;
    }

    /**
    * @ORM\PrePersist()
    */
    public function beforeFirstSave() {
        $this->createdAt = new \DateTime("", new \DateTimeZone("UTC"));
        if (!$this->titleAdmin) {
            $this->titleAdmin = '1';
        }
    }





}
