<?php 
namespace Majes\BlogBundle\Twig;

use Majes\CmsBundle\Entity\Page;
use Majes\CmsBundle\Utils\Helper;

class BlogExtension extends \Twig_Extension
{
   
    private $_em;
    private $_router;
    private $_container;

    public function __construct($em, $router, $container){
        $this->_em = $em;
        $this->_router = $router;
        $this->_container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('articleHasTranslation', array($this, 'articleHasTranslation')),
            new \Twig_SimpleFunction('categHasTranslation', array($this, 'categHasTranslation')),
            new \Twig_SimpleFunction('getLastArticles', array($this, 'getLastArticles'))

        );
    }

    public function articleHasTranslation($article_id, $lang, $admin = false){
        if(empty($article_id)) return false;
        $article = $this->_em->getRepository('MajesBlogBundle:Article')
            ->findOneById($article_id);

        $articleLangs = $article->getLangs();
        foreach($articleLangs as $articleLang){

            if($articleLang->getLocale() == $lang){
                if(!$admin && $articleLang->getIsActive() == false) return false;
                return true;
            }

        }

        return false;

    }

    public function categHasTranslation($category_id, $lang, $admin = false){
        if(empty($category_id)) return false;
        $category = $this->_em->getRepository('MajesBlogBundle:Category')
            ->findOneById($category_id);

        $categoryLangs = $category->getLangs();
        foreach($categoryLangs as $categoryLang){

            if($categoryLang->getLocale() == $lang){
                if(!$admin && $categoryLang->getIsActive() == false) return false;
                return true;
            }

        }

        return false;

    }

    public function getLastArticles($lang, $limit = null, $offset = null){
        
        $articles = $this->_em->getRepository('MajesBlogBundle:ArticleLang')
            ->findBy( array('locale' => $lang), // Critere
                      array('date' => 'desc'),        // Tri
                      $limit,                              // Limite
                      $offset                               // Offset
                    );

        return $articles;

    }


    public function getName()
    {
        return 'majesblog_extension';
    }
}