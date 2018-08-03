<?php

namespace B2bBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use B2bBundle\Entity\MyFile;
use B2bBundle\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ColorProductImportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder  ->add('picture', MyFileType::class, array(
            'label' => false, 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
        ));
        $builder  ->add('color', EntityType::class, array(
            'label' => false,
            'class' => Color::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\ColorProduct'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_colorproductimport';
    }

}
