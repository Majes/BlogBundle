<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BlogType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder

      ->add('host', 'entity', array(
        'required' => true,
        'class' => 'MajesCoreBundle:Host',
        'property' => 'url'))

      ->add('templateIndex', 'entity', array(
        'required' => true,
        'class' => 'MajesCmsBundle:Template',
        'query_builder' => function(EntityRepository $er) {
            return $er->createQueryBuilder('u')
                        ->where('u.deleted = 0');
        },
        'property' => 'title'))

      ->add('templateArticle', 'entity', array(
        'required' => true,
        'class' => 'MajesCmsBundle:Template',
        'query_builder' => function(EntityRepository $er) {
            return $er->createQueryBuilder('u')
                        ->where('u.deleted = 0');
        },
        'property' => 'title'))

      ->add('enableComments', 'checkbox', array(
        'required' => false))

      ->add('isActive', 'checkbox', array(
        'required' => false));
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Majes\BlogBundle\Entity\Blog'
    ));
  }

  public function getName()
  {
    return 'majes_blogbundle_blogtype';
  }
}