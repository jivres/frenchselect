<?php

namespace B2bBundle\Form;

use B2bBundle\Entity\PaymentTerms;
use B2bBundle\Repository\ShopRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('societyName', null, array(
                'label' => 'Nom de la société'
            ))
            ->add('numTVA', null, array(
                'label' => 'Numéro de TVA',
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
            ->add('shop', null, array(
                'label' => 'Boutique',
                'query_builder' => function (ShopRepository $er) use($options) {
                    return $er->queryIn($options['shopIds']);
                },
            ))
            ->add('deliveryAddress', null, array(
                'label' => 'Adresse de livraison',
            ))
            ->add('deliveryZP', null, array(
                'label' => 'Code Postal',
            ))
            ->add('deliveryTown', null, array(
                'label' => 'Ville',
            ))
            ->add('phone', null, array(
                'label' => 'Numéro de téléphone pour la livraison'
            ))
            ->add('comment', null, array(
                'label' => 'Commentaire pour la livraison',
            ))
            /*->add('cart', CartType::class, array(
                'label' => null,
            ))*/;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Command',
            'shopIds' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_command';
    }
}