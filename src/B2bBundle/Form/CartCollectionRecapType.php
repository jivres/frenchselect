<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CartCollectionRecapType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('deliveryDate', DateType::class, array(
                'label' => 'Date de livraison',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => array('class' => 'js-datepicker',
                                'min' => ''),
            ));
        //$builder->get('deliveryDate')->addModelTransformer(new DateTimeTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\CartCollection'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_cartcollectionrecap';
    }
}