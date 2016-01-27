<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class BlogType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder

      ->add('host', 'entity', array(
        'required' => true,
        'class' => 'MajesCoreBundle:Host',
        'choice_label' => 'url'))

      ->add('templateIndex', 'entity', array(
        'required' => true,
        'class' => 'MajesCmsBundle:Template',
        'query_builder' => function(EntityRepository $er) {
            return $er->createQueryBuilder('u')
                        ->where('u.deleted = 0');
        },
        'choice_label' => 'title'))

      ->add('templateArticle', 'entity', array(
        'required' => true,
        'class' => 'MajesCmsBundle:Template',
        'query_builder' => function(EntityRepository $er) {
            return $er->createQueryBuilder('u')
                        ->where('u.deleted = 0');
        },
        'choice_label' => 'title'))

      ->add('enableComments', 'checkbox', array(
        'required' => false))

      ->add('isActive', 'checkbox', array(
        'required' => false));
  }

  public function configureOptions(OptionsResolver $resolver)
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
