<?php

namespace B2bBundle\Form;

use B2bBundle\Entity\Salesman;
use B2bBundle\Repository\BrandRepository;
use B2bBundle\Repository\SalesmanRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesmanShopType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('salesman', EntityType::class, array(
                'label'    => 'Commercial :',
                'class' => Salesman::class,
                'choices' => $options['salesmanIds'],
                'choice_label' => 'firstName',
            ));
        if ($options['editBrand']) {
            $builder->add('brand', null, array(
                'label'    => 'Marque :',
                'attr' => ['class' => 'custom-select'],
                'query_builder' => function (BrandRepository $er) use($options) {
                    return $er->queryIn($options['brandIds']);
                },
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\SalesmanShop',
            'brandIds' => null,
            'salesmanIds' => null,
            'editBrand' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_salesmanshop';
    }
}
