<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductManageType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('priceHT', null, array('label' => 'Prix HT'))
            ->add('recommendedPriceTTC', null, array('label' => 'Prix recommandé TTC'))
            ->add('availabilities', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => AvailabilityType::class,
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
            'data_class' => 'B2bBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_product_manage';
    }
}
