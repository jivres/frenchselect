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

class ColorProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('label', null, array('label' => 'Nom de la couleur'));
        $builder  ->add('picture', MyFileType::class, array(
            'label' => 'Photo du motif', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
        ));
        $builder->add('color', EntityType::class, array(
            'label' => 'Couleur principale',
            'class' => Color::class,
            'choice_label' => 'label',
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
        return 'b2bbundle_colorproduct';
    }

}
