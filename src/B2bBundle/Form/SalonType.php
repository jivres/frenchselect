<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use B2bBundle\Entity\MyFile;
use B2bBundle\Entity\Country;
use B2bBundle\Entity\Salon;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SalonType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', null, array(
                'label' => 'Nom du salon',
            ))
            ->add('pays', EntityType::class, array(
                'label' => 'Pays',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
            ))
            ->add('ville', null, array(
                'label' => 'Ville'
            ))
            ->add('adresse', null, array(
                'label' => 'Adresse'
            ))
            ->add('lieu', null, array(
                'label' => 'Lieu',
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'required' => false,
            ))
            ->add('zipCode', null, array(
                'label' => 'Code postal'
            ))

            ->add('lifestyle', MyFileType::class, array(
                'label' => 'Lifestyle', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
            ))
            ->add('picture', MyFileType::class, array(
                'label' => 'Photo', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
            ))
            ->add('date_debut', DateType::class, array(
                'label' => 'Date de début du salon',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ))

            ->add('date_fin', DateType::class, array(
                'label' => 'Date de fin du salon',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('brands', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => ParticipeSalonType::class,
                'entry_options' => array('label' => false, 'brands' =>$options['brands']),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Salon',
            'brands' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_salon';
    }
}

?>