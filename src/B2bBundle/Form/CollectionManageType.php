<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CollectionManageType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('products', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ProductManageType::class,
                'entry_options' => array('label' => null),
                'by_reference' => false,
                'allow_add' => false,
                'allow_delete' => false,
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Collection'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_collection_manage';
    }
}
