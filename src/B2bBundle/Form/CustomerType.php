<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use B2bBundle\Entity\Country;

class CustomerType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('user', UserType::class, array(
                'label' => false,
                'editPassword' => $options['editPassword']
            ))
            ->add('companyName', null, array(
                'label' => 'Nom de la compagnie',
            ))
            ->add('numTVA', null, array(
                'label' => 'Numéro de TVA',
            ))
            ->add('numSIREN', null, array(
                'label' => 'Numéro SIREN',
            ))
            ->add('limitCredit', null, array(
                'label' => 'Limite de crédit',
            ))
            ->add('deductibleTVA', null, array(
                'label' => 'Société en création / Franchise en base de TVA',
            ))
            ->add('billingAddress', null, array(
                'label' => 'Adresse de facturation',
            ))
            ->add('billingZP', null, array(
                'label' => 'Code Postal',
            ))
            ->add('billingTown', null, array(
                'label' => 'Ville',
            ))
            ->add('billingCountry', EntityType::class, array(
                'label' => 'Pays de facturation',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
            ))
            ->add('phone', null, array(
                'label' => 'Téléphone',
            ))
            ->add('contacts', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ContactCustomerType::class,
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ));
        if($options['shops']) {
           $builder ->
            add('shops', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ShopType::class,
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Customer',
            'shops' => true,
            'editPassword' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_customer';
    }
}
