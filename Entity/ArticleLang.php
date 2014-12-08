<?php
namespace Majes\BlogBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;
use Majes\MediaBundle\Entity\Media;
use Majes\BlogBundle\Entity\CategoryLang;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * Majes\TeelBundle\Entity\Article
 *
 * @ORM\Entity(repositoryClass="Majes\BlogBundle\Entity\ArticleLangRepository")
 * @ORM\Table(name="blog_article_lang")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleLang{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="url", type="string", length=150, nullable=false, unique=true)
     */
    private $url;

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
     * @ORM\Column(name="content_update_date", type="datetime", nullable=false)
     */
    private $contentUpdateDate;

    /**
     * @ORM\Column(name="locale", type="string", length=5, nullable=false)
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\BlogBundle\Entity\Article", inversedBy="langs", cascade={"persist"})
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", nullable=false)
     */
    private $article;

    /**
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content="";

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false)
     */
    private $title="";

    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $media;

    /**
     * @ORM\Column(name="author", type="string", length=150, nullable=false)
     */
    private $author="";

    /**
     * @ORM\Column(name="meta_title", type="string", length=150, nullable=false)
     */
    private $metaTitle="";

    /**
     * @ORM\Column(name="meta_type", type="string", length=150, nullable=false)
     */
    private $metaType="";

    /**
     * @ORM\Column(name="meta_description", type="text", nullable=false)
     */
    private $metaDescription="";

    /**
     * @ORM\ManyToOne(targetEntity="Majes\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="meta_image", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $metaImage;

    /**
     * @ORM\ManyToMany(targetEntity="CategoryLang", inversedBy="articles")
     * @ORM\JoinTable(name="blog_category_article",
     *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     * )
     */
    private $categories;
    


    /**
     * @inheritDoc
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=1)
     */
    public function __construct(){
        $this->createDate = new \DateTime();
        $this->date = new \DateTime();
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();

        $this->contentUpdateDate = new \DateTime();
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
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->contentUpdateDate = new \DateTime();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
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
     * @inheritDoc
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     * @DataTable(label="Title", column="title", isSortable=1)
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     * @DataTable(label="Date", column="date", isSortable=1, format="datetime")
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritDoc
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @inheritDoc
     */
    public function getArticle()
    {
        return $this->article;
    }



    /**
     *
     * @ORM\PrePersist
     */
    public function defaultValues()
    {
        
    }

    /**
     * Gets the value of metaTitle.
     *
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Sets the value of metaTitle.
     *
     * @param mixed $metaTitle the meta title
     *
     * @return self
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Gets the value of metaType.
     *
     * @return mixed
     */
    public function getMetaType()
    {
        return $this->metaType;
    }

    /**
     * Sets the value of metaType.
     *
     * @param mixed $metaType the meta type
     *
     * @return self
     */
    public function setMetaType($metaType)
    {
        $this->metaType = $metaType;

        return $this;
    }

    /**
     * Gets the value of metaDescription.
     *
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Sets the value of metaDescription.
     *
     * @param mixed $metaDescription the meta description
     *
     * @return self
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }


    /**
     * Gets the value of metaImage.
     *
     * @return mixed
     */
    public function getMetaImage()
    {
        return $this->metaImage;
    }

    /**
     * Sets the value of metaImage.
     *
     * @param mixed $metaImage the meta image
     *
     * @return self
     */
    public function setMetaImage(Media $metaImage)
    {
        $this->metaImage = $metaImage;

        return $this;
    }



    /**
     * Gets the value of contentUpdateDate.
     *
     * @return mixed
     */
    public function getContentUpdateDate()
    {
        return $this->contentUpdateDate;
    }

    /**
     * Sets the value of contentUpdateDate.
     *
     * @param mixed $contentUpdateDate the content update date
     *
     * @return self
     */
    public function setContentUpdateDate($contentUpdateDate)
    {
        $this->contentUpdateDate = $contentUpdateDate;

        return $this;
    }

    /**
     * Gets the value of media.
     *
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the value of media.
     *
     * @param mixed $media the media
     *
     * @return self
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Gets the value of author.
     *
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the value of author.
     *
     * @param mixed $author the author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return $this->categories->toArray();
    }

    /**
     * @inheritDoc
     */
    public function addCategory(\Majes\BlogBundle\Entity\CategoryLang $category)
    {
        return $this->categories[] = $category;
    }

    public function hasCategory($category_id){
        $categories = $this->getCategories();

        $categories_array = array();
        foreach($categories as $category){
            if(!$category->getDeleted()){
                $categories_array[] = $category->getId();
            }
        }

        if(in_array($category_id, $categories_array)) return true;
        return false;
    }

    public function removeCategory(\Majes\BlogBundle\Entity\CategoryLang $category)
    {
        return $this->categories->removeElement($category);
    }

    public function removeCategories()
    {
        foreach($this->categories as $category)
            $this->categories->removeElement($category);

        return;
    }
}