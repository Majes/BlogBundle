<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Majes\BlogBundle\Entity\ArticleLang;
use Symfony\Component\HttpFoundation\Session\Session;

class ArticleType extends AbstractType
{
  private $_locale;

  public function __construct($session, $locale){
    $this->_locale=$locale;
  }
	public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $articleLang = $options['data']->getLang();
    if(is_null($options['data']->getLang())){
      $articleLang = new ArticleLang();
      $articleLang->setLocale($this->_locale);
    }
    $builder

      ->add('enableComments', 'checkbox', array(
        'required' => false,
        'label' => 'Enable Comments'))

      ->add('isActive', 'checkbox', array(
        'required' => false,
        'label' => 'is Active'))

      ->add('lang', new ArticleLangType(), array(
            'data' => $articleLang,
        ));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Majes\BlogBundle\Entity\Article'
    ));
  }

  public function getName()
  {
    return 'majes_blogbundle_articletype';
  }
}
