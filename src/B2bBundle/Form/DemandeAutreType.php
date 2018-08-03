<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DemandeAutreType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('type', null, array(
                'label' => 'Demandeur',
                'disabled' => true,
            ))
            ->add('name', null, array(
                'label' => 'Nom',
            ))
            ->add('firstname', null, array(
                'label' => 'Prenom',
            ))
            ->add('mail', EmailType::class, array(
                'label' => 'Mail',
            ))
            ->add('phone', null, array(
                'label' => 'Téléphone',
            ))
            ->add('text', TextareaType::class, array(
                'label' => 'Votre question',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_demandeautre';
    }
}
