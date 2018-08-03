<?php

namespace B2bBundle\Form;

use B2bBundle\Repository\SalesmanRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use B2bBundle\Entity\Country;

class ShopType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', null, array(
                'label' => 'Nom',
            ))
            ->add('address', null, array(
                'label' => 'Adresse',
            ))
            ->add('zipCode', null, array(
                'label' => 'Code Postal',
            ))
            ->add('town', null, array(
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
            ->add('phone', null, array(
                'label' => 'Téléphone',
            ))
            ->add('pic', MyFileType::class, array(
                'label' => 'Photo',
                'data_class' => 'B2bBundle\Entity\MyFile',
                'required' => false,
            ))
            ->add('urlWebsite', null, array(
                'label' => 'Site internet',
                'attr' => array(
                    'placeholder' => 'http://votresite.fr/',
                ),
            ))
            ->add('urlInstagram', null, array(
                'label' => 'Profil Instagram',
            ))
            ->add('urlFacebook', null, array(
                'label' => 'Profil Facebook',
            ))
            ->add('deliverySameAddress', null, array(
                'label' => 'Adresse de livraison identique à l\'adresse de la boutique',
            ))
            ->add('deliveryAddress', null, array(
                'label' => 'Adresse de livraison',
            ))
            ->add('deliveryZP', null, array(
                'label' => 'Code Postal de livraison',
            ))
            ->add('deliveryTown', null, array(
                'label' => 'Ville de livraison',
            ))
            ->add('deliveryComment', null, array(
                'label' => 'Commentaire pour la livraison',
            ));
        if ($options['select-customer']) {
            $builder->add('customer', null, array(
                'label' => 'Client', 'attr' => ['class' => 'custom-select'],
            ));
        }
        /*if ($options['select-salesmen']) {
            $builder->add('salesmen', null, array(
                'label' => 'Commerciaux',
                'by_reference' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['style' => 'column-width:220px'],
                'query_builder' => function (SalesmanRepository $er) use($options) {
                    return $er->queryIn($options['salesmenIds']);
                },
            ));
        }*/
        $builder
            ->add('targets', null, array(
                'label' => 'Cibles',
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))
            ->add('styles', null, array(
                'label' => 'Styles',
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))
            ->add('categories', null, array(
                'label' => 'Catégories',
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
            ))
            ->add('contacts', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ContactCustomerType::class,
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Shop',
            //'select-salesmen' => false,
            'select-customer' => false,
            //'salesmenIds' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_shop';
    }
}
