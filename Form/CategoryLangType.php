<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryLangType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('name', 'text', array(
            'required' => true,
            'label' => 'Name of your Category'))

      ->add('url', 'text', array(
            'required' => true,
            'label' => 'Url of the category'))

      ->add('enable_comments', 'checkbox', array(
        'required' => false,
        'label' => 'Enable Comments in this language'))

      ->add('is_active', 'checkbox', array(
        'required' => false,
        'label' => 'is Active in this language'));
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Majes\BlogBundle\Entity\CategoryLang'
    ));
  }

  public function getName()
  {
    return 'majes_blogbundle_categorylangtype';
  }
}