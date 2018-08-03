<?php

namespace B2bBundle\Form;

use B2bBundle\Entity\Brand;
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

class AssignBrandToCustomerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $brands = $options['brands'];
        $builder
            ->add('brand', null, array(
                'label' => 'Marques',
                'expanded' => true,
                'multiple' => true,
                'choices' => $brands,
                'attr' => ['style' => 'column-width:220px'],
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'brands' => null,
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_assign';
    }
}