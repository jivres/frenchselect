<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date', DateType::class, array(
                'label' => 'Date de la facture',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('dueDate', DateType::class, array(
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('num', null, array(
                'required' => false,
                'label'    => 'Numéro de facture',
            ))
            ->add('paymentMethods', null, array(
                'label' => 'Modes de paiement acceptés',
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))
            ->add('payments', CollectionType::class, array(
                'prototype'     => true,
                'entry_type'    => PaymentType::class,
                'entry_options' => array('label' => null),
                'by_reference'  => false,
                'allow_add'     => true,
                'allow_delete'  => true,
            ))
            ->add('reduction', null, array(
                'label' => 'Réduction commerciale éventuelle',
            ))
            ->add('shippingCosts', null, array(
                'label' => 'Frais de port',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Invoice'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_invoice';
    }
}