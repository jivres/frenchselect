<?php

namespace B2bBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use B2bBundle\Entity\MyFile;

class AllowedColorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['editPictures']) {
            $builder->add('figures', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => MyFileType::class,
                'entry_options' => array('label' => null),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ));
        } else {
            $builder->add('color', ColorProductType::class, array('label' => false));
            $builder->add('refUnique', null, array('label' => 'Référence unique du produit'));
            $builder->add('favourite', null, array('label' => 'Produit Coup de cœur'));
            $builder->add('eanCode', null, array('label' => 'Code Ean'));
            $builder->add('colorCode', null, array('label' => 'Code Couleur'));
            $builder->add('reduction', null, array('label' => 'Réduction %'));
            $builder->add('deliveryStart', DateType::class, array('label' => 'Début de livraison'));
            $builder->add('additionalInformation', TextareaType::class, array('label' => 'Informations complémentaires', 'required' => false));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\AllowedColor',
            'editPictures' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_allowedcolor';
    }

}
