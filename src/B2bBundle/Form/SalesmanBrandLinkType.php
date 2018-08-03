<?php

namespace B2bBundle\Form;

use B2bBundle\Repository\BrandRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesmanBrandLinkType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('departments', null, array(
                'label'    => 'Départements :',
                //'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['style' => 'column-width:220px'],
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
            'data_class' => 'B2bBundle\Entity\SalesmanBrandLink',
            'brandIds' => null,
            'editBrand' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_salesmanbrandlink';
    }
}
