<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use B2bBundle\Entity\Country;

class SalesmanType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('user', UserType::class, array(
                'label' => 'Informations de connexion',
                'editPassword' => $options['editPassword']
            ))
            ->add('companyName', null, array(
                'label' => 'Nom de la société',
            ))
            ->add('numTVA', null, array(
                'label' => 'Numéro de TVA',
            ))
            ->add('deductibleTVA', null, array(
                'label' => 'Société en création / Franchise en base de TVA',
            ))
            ->add('billingAddress', null, array(
                'label' => 'Adresse de facturation',
            ))
            ->add('phone', null, array(
                'label' => 'Numéro de téléphone',
            ))
            ->add('firstName', null, array(
                'label' => 'Prénom',
            ))
            ->add('lastName', null, array(
                'label' => 'Nom',
            ))
            ->add('billingZP', null, array(
                'label' => 'Code Postal',
            ))
            ->add('billingTown', null, array(
                'label' => 'Ville',
            ))
            ->add('country', EntityType::class, array(
                'label' => 'Pays',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
            ))
            ->add('contacts', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ContactBrandType::class,
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ));
            /*->add('departments', null, array(
                'label'    => 'Départements',
                //'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))*/
            /*>add('shops', null, array(
                'label' => 'Boutiques',
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))*/
            /*->add('brands', null, array(
                'label' => 'Marques',
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Salesman',
            'editPassword' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_salesman';
    }
}
