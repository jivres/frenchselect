<?php

namespace B2bBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use B2bBundle\Entity\MyFile;


class CollectionType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', null, array(
                'label' => 'Nom de la collection'))
            ->add('year', null, array(
                'label' => 'Année'))
            ->add('season', null, array(
                'label' => 'Saison', 'attr' => ['class' => 'custom-select']))
            ->add('endDate', DateType::class, array(
                'label' => 'Date de fin des commandes',
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd/MM/yyyy',
            ))
            ->add('lookbook', MyFileType::class, array(
                'label' => 'Lookbook de la collection', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false))

            ->add('lifestyle', MyFileType::class, array(
                'label' => 'Photo Lifestyle', 'data_class' => 'B2bBundle\Entity\MyFile',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Collection'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_collection';
    }
}
