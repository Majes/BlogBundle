<?php
namespace Majes\BlogBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;
use Majes\BlogBundle\Entity\Blog;


/**
 * Majes\TeelBundle\Entity\Category
 *
 * @ORM\Entity(repositoryClass="Majes\BlogBundle\Entity\CategoryRepository")
 * @ORM\Table(name="blog_category")
 * @ORM\HasLifecycleCallbacks
 */
class Category{
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
     * @ORM\JoinColumn(name="blog_id", referencedColumnName="id", nullable=false)
     */
    private $blog;

    /**
     * @ORM\OneToMany(targetEntity="Majes\BlogBundle\Entity\CategoryLang", mappedBy="category", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="article_id")
     */
    private $langs;

    /**
     * Current lang
     */
    private $lang = null;


    /**
     * @inheritDoc
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=1)
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
     * @DataTable(label="Id", column="id", isSortable=1)
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
     * @DataTable(label="Date", column="date", isSortable=1, format="datetime")
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



    /**
     * Gets the value of deleted.
     *
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Sets the value of deleted.
     *
     * @param mixed $deleted the deleted
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Gets the value of blog.
     *
     * @return mixed
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Sets the value of blog.
     *
     * @param mixed $blog the blog
     *
     * @return self
     */
    public function setBlog(Blog $blog)
    {
        $this->blog = $blog;

        return $this;
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
    public function addLang(\Majes\BlogBundle\Entity\CategoryLang $lang)
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

}