<?php

namespace B2bBundle\Form;


use B2bBundle\Entity\ColorProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use B2bBundle\Entity\MyFile;
use B2bBundle\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AllowedColorImportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder  ->add('color', EntityType::class, array(
           'label' => false,
            'class' => ColorProduct::class,
        ));
        $builder->add('figures', CollectionType::class, array(
            'label' => false,
            'prototype' => true,
            'entry_type' => MyFileType::class,
            'entry_options' => array('label' => false),
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\AllowedColor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_allowedcolorimport';
    }

}
