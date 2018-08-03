<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use B2bBundle\Entity\MyFile;
use B2bBundle\Entity\Country;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BrandEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserType::class, array(
                'label' => false,
                'editPassword' => $options['editPassword']
            ))
            ->add('name', null, array(
                'label' => 'Nom de la société',
            ))
            ->add('brandName', null, array(
                'label' => 'Nom de la marque',
            ))
            ->add('numTVA', null, array(
                'label' => 'Numéro de TVA',
            ))
            ->add('deductibleTVA', null, array(
                'label' => 'Société en création / Franchise en base de TVA',
            ))
            ->add('numSIREN', null, array(
                'label' => 'Numéro SIREN',
            ))
            ->add('RCSTown', null, array(
                'label' => 'Ville RCS',
            ))
            ->add('APECode', null, array(
                'label' => 'Code APE',
            ))
            ->add('capital', NumberType::class, array(
                'label' => 'Montant du capital',
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
            ->add('commandMin', null, array(
                'label' => 'Minimum de commande',
            ))
            ->add('franco', null, array(
                'label' => 'Franco de port',
            ))
            ->add('deliveryCharges', null, array(
                'label' => 'Frais de port si forfaitaire',
            ))
            ->add('yearCreation', null, array(
                'label' => 'Année de création',
            ))
            ->add('urlWebsite', null, array(
                'label' => 'Site internet',
            ))
            ->add('urlInsta', null, array(
                'label' => 'Profil Instagram',
            ))
            ->add('urlFac', null, array(
                'label' => 'Profil Facebook',
            ))
            ->add('marginAvg', null, array(
                'label' => 'Marge commerciale conseillée',
            ))
            ->add('slogan', null, array(
                'label' => 'Slogan (phrase de présentation)',
            ))
            ->add('description', null, array(
                'label' => 'Description',
            ))
            ->add('country', EntityType::class, array(
                'label' => 'Pays de la marque',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
            ))
            ->add('manufactureCountry', EntityType::class, array(
                'label' => 'Pays de fabrication',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
                'required' => false
            ))
            ->add('paymentMethods', null, array(
                'label' => 'Moyens de paiement',
                'expanded' => true,
                'multiple' => true,
                'attr' => ['style' => 'column-width:220px'],
            ))
            ->add('paymentTerms', null, array(
                'label' => 'Délais de paiement autorisés',
                'expanded' => true,
                'multiple' => true,
                'attr' => ['style' => 'column-width:220px'],
            ))
            ->add('CGV', MyFileType::class, array(
                'label' => 'Conditions Générales de Vente', 'data_class' => 'B2bBundle\Entity\MyFile',
            ))
            ->add('contacts', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ContactBrandType::class,
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
            'data_class' => 'B2bBundle\Entity\Brand',
            'editPassword' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_brand';
    }
}
