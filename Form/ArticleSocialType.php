<?php

namespace Majes\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Majes\MediaBundle\Entity\Media;

class ArticleSocialType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $builder->add('metaType', 'text', array(
            'required' => true,
            'label' => 'Type'))

      ->add('metaTitle', 'text', array(
          'required' => true,
          'label' => 'Title'))

      ->add('metaDescription', 'textarea', array(
        'required' => false,
        'label' => 'Description'))

      ->add('metaImage', 'file', array(
        'mediapicker' => true,
        'data' => $options['data']->getMetaImage(),
        'data_class' => 'Majes\MediaBundle\Entity\Media',
        'required' => false,
        'label' => 'Image',
        'mapped' => false));
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
