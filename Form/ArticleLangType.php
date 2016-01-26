<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleLangType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $locale = $options['data']->getLocale();

    $builder->add('url', 'text', array(
            'required' => true,
            'label' => 'Url of your page'))

      ->add('categories', 'entity', array(
        'required' => true,
        'multiple' => true,
        'select2' => true,
        'class' => 'MajesBlogBundle:CategoryLang',
        'query_builder' => function(EntityRepository $er) use ($locale) {
            return $er->createQueryBuilder('u')
                        ->where('u.locale LIKE ?0')
                        ->andWhere('u.deleted = 0')
                        ->setParameter(0,$locale);
        },
        'property' => 'name'))

      ->add('enable_comments', 'checkbox', array(
        'required' => false,
        'label' => 'Enable Comments in this language'))

      ->add('is_active', 'checkbox', array(
        'required' => false,
        'label' => 'is Active in this language'));
  }

  public function configureOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Majes\BlogBundle\Entity\ArticleLang'
    ));
  }

  public function getName()
  {
    return 'majes_blogbundle_articlelangtype';
  }
}
