<?php
namespace Majes\BlogBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\TeelBundle\Entity\Blog
 *
 * @ORM\Entity(repositoryClass="Majes\BlogBundle\Entity\BlogRepository")
 * @ORM\Table(name="blog_blog")
 * @ORM\HasLifecycleCallbacks
 */
class Blog{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive=1;

    /**
     * @ORM\Column(name="enable_comments", type="boolean", nullable=false)
     */
    private $enableComments=0;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\Host")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id", nullable=false, unique=true)
     */
    private $host;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_article_id", referencedColumnName="id", nullable=false)
     */
    private $templateArticle;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_index_id", referencedColumnName="id", nullable=false)
     */
    private $templateIndex;


    /**
     * @inheritDoc
     */
    public function __construct(){
        $this->createDate = new \DateTime();
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEnableComments($enableComments)
    {
        $this->enableComments = $enableComments;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateArticle(\Majes\CmsBundle\Entity\Template $templateArticle)
    {
        $this->templateArticle = $templateArticle;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateIndex(\Majes\CmsBundle\Entity\Template $templateIndex)
    {
        $this->templateIndex = $templateIndex;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHost(\Majes\CoreBundle\Entity\Host $host)
    {
        $this->host = $host;
        return $this;
    }

    
    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @inheritDoc
     */
    public function getEnableComments()
    {
        return $this->enableComments;
    }

    /**
     * @inheritDoc
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @inheritDoc
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateArticle()
    {
        return $this->templateArticle;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateIndex()
    {
        return $this->templateIndex;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdateDate(new \DateTime(date('Y-m-d H:i:s')));

        if($this->getCreateDate() == null)
        {
            $this->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function defaultValues()
    {
        
    }
}