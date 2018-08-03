<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactCustomerType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('function', null, array(
                'label' => 'Fonction', 'attr' => ['class' => 'custom-select'],
            ))
            ->add('lastname', null, array(
                'label' => 'Nom',
            ))
            ->add('firstname', null, array(
                'label' => 'Prénom',
            ))
            ->add('mail', null, array(
                'label' => 'Adresse email',
            ))
            ->add('phone', null, array(
                'label' => 'Téléphone',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\ContactCustomer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_contact';
    }
}
