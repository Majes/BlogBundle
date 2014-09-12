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
use Majes\BlogBundle\Form\CategoryType;
use Majes\BlogBundle\Form\CategoryLangType;
use Majes\BlogBundle\Entity\Blog;
use Majes\BlogBundle\Entity\Article;
use Majes\BlogBundle\Entity\Category;
use Majes\BlogBundle\Entity\ArticleLang;
use Majes\BlogBundle\Entity\CategoryLang;
use Majes\CmsBundle\Entity\Block;
use Majes\MediaBundle\Entity\Media;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AdminController extends Controller implements SystemController
{
    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     *
     */
    public function contentAction()
    {   

        return $this->render('MajesBlogBundle:Admin:content.html.twig', array(
            'pageTitle' =>'Blog Management',
            'pageSubTitle' => '',
            'form' =>null,
            'blog' => null,
            'articles' => null,
            'edit' => null,
            'edit_article' => null,
            'edit_categ' => null,
            'setup' => null,
            'blocks' => null,
            'page_has_draft' => null,
            'lang' => 'fr',
            'form_role' => null
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     *
     */
    public function contentListAction($blog)
    {   
        $em = $this->getDoctrine()->getManager();
        
        $articles = $em->getRepository('MajesBlogBundle:Article')->findBy(array('blog' => $blog));

        $filter = array();
        foreach($articles as $article){
            $filter[]=$article->getId();
        }
        if(!is_null($articles)){
            $articles = $em->getRepository('MajesBlogBundle:ArticleLang')->findBy(array('article' => $filter, 'locale' => $this->_lang));
        }else{
            $articles=array();
        }
        

        $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

        return $this->render('MajesBlogBundle:Admin:content.html.twig', array(
            'pageTitle' =>'Blog Management',
            'pageSubTitle' => '',
            'form' => null,
            'blog' => $blog,
            'object' => new ArticleLang(),
            'datas' => $articles,
            'edit' => true,
            'edit_article' => null,
            'edit_categ' => null,
            'setup' => null,
            'blocks' => null,
            'page_has_draft' => null,
            'lang' => 'fr',
            'form_role' => null,
            'message' => 'Are you sure you want to delete this article ?',
            'urls' => array('params' => array('blog'=> $blog->getId(), 'id' => $article->getId()),
                            'add' => '_blog_article_edit',
                            'edit' => '_blog_article_edit',
                            'delete' => '_blog_article_delete')
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     *
     */
    public function articleEditAction($blog, $id)
    {   
        $request = $this->getRequest();
        $accessor = PropertyAccess::createPropertyAccessor();
        $em = $this->getDoctrine()->getManager();

        $session = $this->get('session');

        $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

        $article = $em->getRepository('MajesBlogBundle:Article')->findOneById($id);

        if(is_null($article))
            $article = new Article();

        $article->setLang($this->_lang);

        $formArticle = $this->createForm(new ArticleType($request->getSession(), $this->_lang), $article);

        $formArticleSocial = null;
        $blocks = null;
        $attributes = array();
        $block = array();

        if(!is_null($article->getId())){
            $copy=$request->get('copy');
            if(is_null($article->getLang()) && $copy){
                $article->setLang($this->_default_lang);
                $new_lang = clone $article->getLang();
                $new_lang->setLocale($this->_lang);
                $new_lang->setUrl($new_lang->getUrl().'-'.$this->_lang);
                $new_lang->setCreateDate(new \DateTime());

                $article->addLang($new_lang);

                $em->persist($article);
                $em->flush();
                $article->setLang($this->_lang);
                $formArticle = $this->createForm(new ArticleType($request->getSession(), $this->_lang), $article);
            }elseif (is_null($article->getLang())) {
                $new_lang = new ArticleLang();
                $new_lang->setLocale($this->_lang);
                //$new_lang->setUrl('');
                $article->addLang($new_lang);

                // $em->persist($article);
                // $em->flush();
                $article->setLang($this->_lang);
                $formArticle = $this->createForm(new ArticleType($request->getSession(), $this->_lang), $article);
            }

            $formArticleSocial = $this->createForm(new ArticleSocialType($request->getSession()), $article->getLang(), array('action' => $this->get('router')->generate('_blog_article_social_edit', array('blog' => $blog->getId(), 'id' => $article->getId()))))->createView();

            $attributesSetted=$session->get('menu')['cms']['submenu']['blog']['attributes'];

            foreach ($attributesSetted as $key => $value) {
                $attribute = $em->getRepository('MajesCmsBundle:Attribute')->findOneById($value);
                $attributes[$key] = array('attribute' => $attribute, 'value' => $accessor->getValue($article->getLang(), $key));
            }

            $block = array('title' => 'Content',
                            'update_date' => $article->getLang()->getContentUpdateDate(),
                            'attributes' => $attributes);

        }

        if($request->getMethod() == 'POST'){
            
            $formArticle->handleRequest($request);

            if ($formArticle->isValid()) {
                if(is_null($article->getId())){
                    $article->setBlog($blog);
                    $article = $formArticle->getData();

                    $articleLang = $formArticle->get('lang')->getData();
                    $articleLang->setLocale($this->_lang);
                    $articleLang->setArticle($article);
                    
                    $article->addLang($articleLang);
                }else{
                    
                    $article=$formArticle->getData();
                    if(is_null($article->getLang()->getId()))
                        $article->getLang()->setArticle($article);
                }
                

                $em->persist($article);
                $em->flush();

                return $this->redirect($this->generateUrl('_blog_article_edit', array('blog' => $blog->getId(), 'id' => $article->getId())));
            }
        }


        return $this->render('MajesBlogBundle:Admin:content.html.twig', array(
            'pageTitle' =>'Blog Management',
            'pageSubTitle' => 'Article Edit',
            'form' => $formArticle->createView(),
            'blog' => $blog,
            'article' => $article->getId(),
            'object' => new Article(),
            'datas' => null,
            'edit' => null,
            'attributes' => $attributes,
            'edit_article' => true,
            'edit_categ' => null,
            'setup' => null,
            'block' => $block,
            'page_has_draft' => null,
            'lang' => 'fr',
            'form_social' => $formArticleSocial,
            'urls' => array('add' => '_blog_article_edit')
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function articleContentFormAction(){

        $request = $this->getRequest();
        $accessor = PropertyAccess::createPropertyAccessor();
        $session = $this->get('session');
        
        if($request->isXmlHttpRequest()){

            $blog = $request->get('blog');
            $article = $request->get('article');
            
            $em = $this->getDoctrine()->getManager();

            $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

            $article = $em->getRepository('MajesBlogBundle:Article')->findOneById(array($article));

            $article->setLang($this->_lang);

            $attributesSetted=$session->get('menu')['cms']['submenu']['blog']['attributes'];

            $attributes = array();
            $values = array();
            foreach ($attributesSetted as $key => $value) {
                $attribute = $em->getRepository('MajesCmsBundle:Attribute')->findOneById($value);
                $attributes[$key] = array('attribute' => $attribute, 'value' => $accessor->getValue($article->getLang(), $key));
            }
            


            return $this->render('MajesBlogBundle:Admin:parts/form-block.html.twig', array(
                'article' => $article->getLang(),
                'blog' => $blog,
                'lang' => $this->_lang,
                'attributes' => $attributes

            ));

        }else
            return new Response();
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     *
     */
    public function articleContentEditAction()
    {   
        $request = $this->getRequest();
        $accessor = PropertyAccess::createPropertyAccessor();
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');

        if($request->getMethod() == 'POST'){
            $articleLang = $em->getRepository('MajesBlogBundle:ArticleLang')->findOneBy(array('id' => $request->get('id'), 'locale' => $request->get('lang')));
            $attributesSetted=$session->get('menu')['cms']['submenu']['blog']['attributes'];
            foreach ($attributesSetted as $key => $id) {
                
                switch($id){
                    case 2:
                        
                        $metaImage = $request->get('attributes_'.$key);
                        $metaImage['value'] = $request->files->get('attributes_'.$key)['value'];
                
                        if(isset($metaImage['remove'])){
                            $value = null;
                        }else if(!is_null($metaImage['value'])){
                            
                            $media = new Media();
                            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
                            $media->setUser($this->_user);
                            $media->setFolder('Blog');
                            $media->setType('picture');
                            $media->setFile($metaImage['value']);
                            $media->setTitle($metaImage['author']);
                            $media->setAuthor($metaImage['title']);


                            $em->persist($media);
                            $em->flush();
                            $value = $media;
                       
                        }else{
                            $value = $em->getRepository('MajesMediaBundle:Media')->findOneById($request->get('attributes_'.$key));
                        }
                        break;
                    case 9:
                        $value = new \DateTime($request->get('attributes_'.$key));
                        break;
                    default:
                        $value=$request->get('attributes_'.$key);
                        break;
                }
                $accessor->setValue($articleLang, $key, $value);

            }


                $em->persist($articleLang);
                $em->flush();

                return $this->redirect($this->generateUrl('_blog_article_edit', array('blog' => $request->get('blog'), 'id' => $articleLang->getArticle()->getId())));
        }
    }


    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     *
     */
    public function articleSocialEditAction($blog, $id)
    {   
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

        $article = $em->getRepository('MajesBlogBundle:Article')->findOneById(array($id));

        if(is_null($article))
            $article = new Article();

        $article->setLang($this->_lang);

        $formArticleSocial = $this->createForm(new ArticleSocialType($request->getSession()), $article->getLang(), array('action' => $this->get('router')->generate('_blog_article_social_edit', array('blog' => $blog->getId(), 'id' => $article->getId()))));

        if($request->getMethod() == 'POST'){
            $formArticleSocial->handleRequest($request);
            
            if ($formArticleSocial->isValid()) {
                $metaImage = $formArticleSocial->get('metaImage')->getData();
                
                if(isset($metaImage['remove'])){
                    $metaImage = null;
                }else if(!is_null($metaImage['value'])){
                    
                    $media = new Media();
                    $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
                    $media->setUser($this->_user);
                    $media->setFolder('Blog');
                    $media->setType('picture');
                    $media->setFile($metaImage['value']);

                    $em->persist($media);
                    $em->flush();
                    $metaImage = $media;
               
                }else{
                    $metaImage = $em->getRepository('MajesMediaBundle:Media')->findOneById($metaImage['id']);
                }
                
                $articleLang = $article->getLang();
                $articleLang = $formArticleSocial->getData();
                $articleLang->setMetaImage($metaImage);

                $em->persist($articleLang);
                $em->flush();

                return $this->redirect($this->generateUrl('_blog_article_edit', array('blog' => $blog->getId(), 'id' => $article->getId())));
            }
        }


        return $this->redirect($this->generateUrl('_blog_article_edit', array('blog' => $blog->getId(), 'id' => $article->getId())));

    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function categoryEditAction($blog, $id){

        $request = $this->getRequest();
        $accessor = PropertyAccess::createPropertyAccessor();
        $em = $this->getDoctrine()->getManager();

        $session = $this->get('session');

        $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

        $category = $em->getRepository('MajesBlogBundle:Category')->findOneById($id);

        if(is_null($category))
            $category = new Category();

        $category->setLang($this->_lang);

        $formCategory = $this->createForm(new CategoryType($request->getSession()), $category);


        if(!is_null($category->getId())){
            $copy=$request->get('copy');
            if(is_null($category->getLang()) && $copy){
                $category->setLang($this->_default_lang);
                $new_lang = clone $category->getLang();
                $new_lang->setLocale($this->_lang);
                $new_lang->setUrl($new_lang->getUrl().'-'.$this->_lang);
                $new_lang->setCreateDate(new \DateTime());

                $category->addLang($new_lang);

                $em->persist($category);
                $em->flush();
                $category->setLang($this->_lang);
                $formCategory = $this->createForm(new CategoryType($request->getSession()), $category);
            }elseif (is_null($category->getLang())) {
                $new_lang = new CategoryLang();
                $new_lang->setLocale($this->_lang);
                
                $category->addLang($new_lang);

                $category->setLang($this->_lang);
                $formCategory = $this->createForm(new CategoryType($request->getSession()), $category);
            }
        }

        if($request->getMethod() == 'POST'){
            
            $formCategory->handleRequest($request);

            if ($formCategory->isValid()) {
                if(is_null($category->getId())){
                    $category->setBlog($blog);
                    $category = $formCategory->getData();

                    $categoryLang = $formCategory->get('lang')->getData();
                    $categoryLang->setLocale($this->_lang);
                    $categoryLang->setCategory($category);
                    
                    $category->addLang($categoryLang);
                }else{
                    
                    $category=$formCategory->getData();
                    if(is_null($category->getLang()->getId()))
                        $category->getLang()->setArticle($category);
                }
                

                $em->persist($category);
                $em->flush();

                //return $this->redirect($this->generateUrl('_blog_category_edit', array('blog' => $blog->getId(), 'id' => $category->getId())));
            }
        }


        return $this->render('MajesBlogBundle:Admin:content.html.twig', array(
            'pageTitle' =>'Blog Management',
            'pageSubTitle' => 'Category Edit',
            'form' => $formCategory->createView(),
            'blog' => $blog,
            'article' => $category->getId(),
            'object' => new Category(),
            'datas' => null,
            'edit' => null,
            'attributes' => null,
            'edit_article' => null,
            'edit_categ' => true,
            'setup' => null,
            'block' => null,
            'page_has_draft' => null,
            'lang' => 'fr',
            'form_social' => null,
            'urls' => array('add' => '_blog_article_edit')
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function categoriesAction($blog){

        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('MajesBlogBundle:Category')
            ->findBy(array('blog' => $blog));

        $filter = array();
        foreach($categories as $category){
            $filter[]=$category->getId();
        }
        if(!is_null($categories)){
            $categories = $em->getRepository('MajesBlogBundle:CategoryLang')->findBy(array('category' => $filter, 'locale' => $this->_lang));
        }else{
            $categories=array();
        }
        


        $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

        return $this->render('MajesBlogBundle:Admin:content.html.twig', array(
            'pageTitle' =>'Blog Management',
            'pageSubTitle' => '',
            'form' => null,
            'blog' => $blog,
            'object' => new CategoryLang(),
            'datas' => $categories,
            'edit' => true,
            'edit_article' => null,
            'edit_categ' => null,
            'setup' => null,
            'blocks' => null,
            'page_has_draft' => null,
            'lang' => 'fr',
            'form_role' => null,
            'message' => 'Are you sure you want to delete this category ?',
            'urls' => array('params' => array('blog'=> $blog->getId(), 'id' => $category->getId()),
                            'add' => '_blog_category_edit',
                            'edit' => '_blog_category_edit',
                            'delete' => '_blog_article_delete')
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function menuAction($id){

        $em = $this->getDoctrine()->getManager();

        //Get blogs
        $blogs = $em->getRepository('MajesBlogBundle:Blog')
            ->findAll();

        $menu = array();

        return $this->render('MajesBlogBundle:Admin:parts/tree.html.twig', array(
            'blogs' => $blogs, 
            'url' => array(
                'setup' => '_blog_setup',
                'edit' => '_blog_content_edit',
                'edit_categ' => '_blog_categories_edit')));
    }


    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function setupAction($blog){

        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        //Get blog
        $blog = $em->getRepository('MajesBlogBundle:Blog')
            ->findOneById($blog);

        $form = $this->createForm(new BlogType($request->getSession()), $blog);

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);
            if ($form->isValid()) {

                $blog = $form->getData();

                $em->persist($blog);
                $em->flush();
            }
        }

        return $this->render('MajesBlogBundle:Admin:content.html.twig', array(
            'pageTitle' =>'Blog Management',
            'pageSubTitle' => 'Blog Setup',
            'form' =>$form->createView(),
            'blog' => $blog,
            'articles' => null,
            'edit' => null,
            'setup' => true,
            'edit_article' => null,
            'edit_categ' => null,
            'blocks' => null,
            'page_has_draft' => null,
            'lang' => null,
            'form_role' => null
            ));
    }
}
