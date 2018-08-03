<?php

namespace B2bBundle\Form;

use B2bBundle\Entity\Brand;
use B2bBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use B2bBundle\Entity\MyFile;
use B2bBundle\Entity\Country;
use B2bBundle\Entity\ParticipeSalon;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AssignCustomerType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $brands = $options['brands'];
        $customers = $options['customers'];
        $builder
            ->add('brand', EntityType::class, array(
                'label' => 'Marque',
                'class' => Brand::class,
                'choices' => $brands,
                'choice_label' => 'brandName',
            ))
            ->add('customer', EntityType::class, array(
            'label' => 'Client',
            'class' => Customer::class,
            'choices' => $customers,
            'choice_label' => 'companyName',
    ));
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'brands' => null,
            'customers' => null,
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_assignCustomer';
    }


}