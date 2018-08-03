<?php

namespace B2bBundle\Form;

use B2bBundle\Entity\AllowedSize;
use B2bBundle\Entity\Availability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use B2bBundle\Entity\Country;
use B2bBundle\Entity\Size;

class ProductType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('refModel', null, array('label' => 'Référence'))
            ->add('name', null, array('label' => 'Nom'))
            ->add('label', null, array('label' => 'Libellé complet'))
            ->add('description', null, array('label' => 'Description'))
            ->add('priceHT', null, array('label' => 'Prix d\'achat H.T.'))
            ->add('recommendedPriceTTC', null, array('label' => 'Prix de vente recommandé T.T.C.'))
            ->add('material', null, array('label' => 'Composition'))
            ->add('maintenance', null, array('label' => 'Entretien'))
            ->add('dimensions', null, array('label' => 'Dimensions'))
            ->add('primaryCat', null, array('label' => 'Catégorie Primaire'))
            ->add('secondaryCat', null, array('label' => 'Catégorie Secondaire'))
            ->add('tertiaryCategory', null, array('label' => 'Catégorie tertiaire'))
            ->add('target', null, array('label' => 'Cible'))
            ->add('country', EntityType::class, array(
                'label' => 'Pays de fabrication',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
            ))
            ->add('sizes', EntityType::class, array(
                'label' => false,
                'class' => Size::class,
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr' => ['class' => 'form-check-inline'],
            ))
            ->add('mainPicture', MyFileType::class, array(
                'label' => 'Photo principale', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false,
            ));
        if($options['color']) {
            $builder ->add('allowedColors', CollectionType::class, array(
                'prototype' => true,
                'entry_type' => AllowedColorType::class,
                'entry_options' => array('label' => null),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ));
         }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Product',
            'color' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_product';
    }
}
