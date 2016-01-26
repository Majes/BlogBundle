<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class CategoryType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder

      ->add('enableComments', 'checkbox', array(
        'required' => false,
        'label' => 'Enable Comments'))

      ->add('isActive', 'checkbox', array(
        'required' => false,
        'label' => 'is Active'))

      ->add('lang', new CategoryLangType());
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Majes\BlogBundle\Entity\Category'
    ));
  }

  public function getName()
  {
    return 'majes_blogbundle_categorytype';
  }
}
