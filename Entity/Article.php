<?php
namespace Majes\BlogBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\TeelBundle\Entity\Article
 *
 * @ORM\Entity(repositoryClass="Majes\BlogBundle\Entity\ArticleRepository")
 * @ORM\Table(name="blog_article")
 * @ORM\HasLifecycleCallbacks
 */
class Article{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive=true;

    /**
     * @ORM\Column(name="enable_comments", type="boolean", nullable=false)
     */
    private $enableComments=false;
    
    /**
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted=false;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\BlogBundle\Entity\Blog")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $blog;

    /**
     * @ORM\OneToMany(targetEntity="Majes\BlogBundle\Entity\ArticleLang", mappedBy="article", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="article_id")
     */
    private $langs;

    /**
     * Current lang
     */
    private $lang = null;


    /**
     * @inheritDoc
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=0)
     */
    public function __construct(){
        $this->createDate = new \DateTime();
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function setBlog($blog)
    {
        $this->blog = $blog;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    
    /**
     * @inheritDoc
     * @DataTable(label="Id", column="id", isSortable=0)
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
     * @DataTable(label="Blog", column="blog_id", isSortable=0)
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @inheritDoc
     */
    public function getDeleted()
    {
        return $this->deleted;
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
     * @inheritDoc
     */
    public function setLang($locale)
    {
        if(is_null($this->langs)){
            $this->lang = null;
            return $this;
        }

        foreach ($this->langs as $lang) {
            if($lang->getLocale() == $locale){
                $this->lang = $lang;
                break;
            }
        }
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function addLang(\Majes\BlogBundle\Entity\ArticleLang $lang)
    {
        return $this->langs[] = $lang;
    }

    public function hasLang($locale){
        $langs = $this->getLangs();

        $roles_array = array();
        foreach($langs as $lang){
            if($locale == $lang->getLocale()) return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getLangs()
    {
        return $this->langs->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getLang()
    {

        return $this->lang;
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function defaultValues()
    {
        
    }
}