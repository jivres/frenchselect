<?php

namespace B2bBundle\Form;

use B2bBundle\Repository\DepartementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesmanDepartmentType extends AbstractType {
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
                'query_builder' => function (DepartementRepository $er) use($options) {
                    return $er->queryIn($options['departmentIds']);
                },
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Salesman',
            'departmentIds' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_salesman_department';
    }
}
