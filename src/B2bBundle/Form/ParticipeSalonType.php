<?php

namespace B2bBundle\Form;

use B2bBundle\Entity\Brand;
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

class ParticipeSalonType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $brands = $options['brands'];
        $builder
            ->add('brand', EntityType::class, array(
                'label' => 'Marque',
                'class' => Brand::class,
                'choices' => $brands,
                'choice_label' => 'brandName',
            ))
            ->add('stand', null, array(
                'label' => 'Stand',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\ParticipeSalon',
            'brands' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'b2bbundle_participesalon';
    }
}

?>