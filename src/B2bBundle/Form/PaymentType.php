<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date', DateType::class, array(
                'label' => 'Date du paiment',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('amount', null, array(
                'label' => 'Montant',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Payment'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_payment';
    }
}