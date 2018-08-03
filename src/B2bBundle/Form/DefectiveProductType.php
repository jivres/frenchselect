<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DefectiveProductType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('quantity', null, array('label' => false))
            ->add('pictures', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => MyFileType::class,
                'entry_options' => array('label' => null),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('comment', null, array(
                'label' => false,
            ))
            ;

        /*->add('logo', MyFileType::class, array(
            'label' => 'Logo', 'data_class' => 'B2bBundle\Entity\MyFile',
        ))*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\DefectiveProduct'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_defective_product';
    }
}