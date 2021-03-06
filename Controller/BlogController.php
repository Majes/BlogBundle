<?php

namespace Majes\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Majes\CoreBundle\Controller\SystemController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\Common\Annotations\AnnotationReader;
use Majes\BlogBundle\Form\BlogType;
use Majes\BlogBundle\Form\ArticleType;
use Majes\BlogBundle\Form\ArticleLangType;
use Majes\BlogBundle\Form\ArticleSocialType;
use Majes\BlogBundle\Entity\Blog;
use Majes\BlogBundle\Entity\Article;
use Majes\BlogBundle\Entity\ArticleLang;
use Majes\CmsBundle\Entity\Block;
use Majes\MediaBundle\Entity\Media;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller implements SystemController
{

    public function indexAction(Request $request, $category = null)
    {

        $em = $this->getDoctrine()->getManager();

        $host = $em->getRepository('MajesCoreBundle:Host')->findOneBy(array('url' => $request->getHost()));

        $blog = $em->getRepository('MajesBlogBundle:Blog')->findOneBy(array('host' => $host->getId()));

        $categoryLang = $em->getRepository('MajesBlogBundle:CategoryLang')->findOneBy(array('url' => $category, 'locale' => $this->_lang, 'isActive' => 1, 'deleted' => 0));

        $categories = $em->getRepository('MajesBlogBundle:CategoryLang')->findBy(array('locale' => $this->_lang, 'isActive' => 1, 'deleted' => 0));

        if(!$blog->getIsActive() || ( is_null($categoryLang) && !is_null($category) ) || ( !is_null($categoryLang) && ( !$categoryLang->getIsActive() || !$categoryLang->getCategory()->getIsActive() ) ) )
            throw new \Exception(404);


        $articleLangs = is_null($categoryLang) ? $em->getRepository('MajesBlogBundle:ArticleLang')->findBy(array('locale' => $this->_lang, 'isActive' => 1, 'deleted' => 0)) : $categoryLang->getArticles();

        return $this->render('MajesTeelBundle:Cms:templates/'.$blog->getTemplateIndex()->getRef().'.html.twig', array(
            'articles' => $articleLangs,
            'category' => $categoryLang,
            'blog' => $blog,
            'categories'=> $categories
            ));
    }


    public function contentAction(Request $request, $category, $url)
    {

        $em = $this->getDoctrine()->getManager();

        $host = $em->getRepository('MajesCoreBundle:Host')->findOneBy(array('url' => $request->getHost()));

        $blog = $em->getRepository('MajesBlogBundle:Blog')->findOneBy(array('host' => $host->getId()));

        $categoryLang = $em->getRepository('MajesBlogBundle:CategoryLang')->findOneBy(array('url' => $category, 'locale' => $this->_lang, 'isActive' => 1, 'deleted' => 0));

        $articleLang = $em->getRepository('MajesBlogBundle:ArticleLang')->findOneBy(array('url' => $url, 'locale' => $this->_lang, 'isActive' => 1, 'deleted' => 0));

        $categories = $em->getRepository('MajesBlogBundle:CategoryLang')->findBy(array('locale' => $this->_lang, 'isActive' => 1, 'deleted' => 0));

        if(!$blog->getIsActive() || is_null($categoryLang) || is_null($articleLang)  || ( !is_null($categoryLang) && ( !$categoryLang->getIsActive() || !$categoryLang->getCategory()->getIsActive() ) || ( !is_null($articleLang) && ( !$articleLang->getIsActive() || !$articleLang->getArticle()->getIsActive() ) ) ) )
            throw new \Exception(404);
        if(!is_null($categoryLang) && !$articleLang->hasCategory($categoryLang->getId()))
            throw new \Exception(404);



        return $this->render('MajesTeelBundle:Cms:templates/'.$blog->getTemplateArticle()->getRef().'.html.twig', array(
            'article' => $articleLang,
            'category' => $categoryLang,
            'blog' => $blog,
            'categories'=> $categories
            ));
    }
}
