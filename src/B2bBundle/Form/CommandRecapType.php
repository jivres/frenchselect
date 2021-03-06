<?php
/**
 * Created by PhpStorm.
 * User: sowipheur
 * Date: 05/12/2017
 * Time: 21:17
 */

namespace B2bBundle\Form;

use B2bBundle\Entity\PaymentTerms;
use B2bBundle\Repository\PaymentMethodRepository;
use B2bBundle\Repository\PaymentTermsRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandRecapType  extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            /*->add('cartCollection', CartRecapType::class, array(
                'label' => null,
            ))*/
            ->add('paymentMethod', null, array(
                'label' => 'Modes de paiement',
                'class' => 'B2bBundle:PaymentMethod',
                'required' => true,
                'query_builder' => function (PaymentMethodRepository $er) use($options) {
                    return $er->queryIn($options['paymentMethodIds']);
                },
            ))
            ->add('paymentTerms', null, array(
                'label' => 'Conditions de paiement',
                'class' => 'B2bBundle:PaymentTerms',
                'required' => true,
                'query_builder' => function (PaymentTermsRepository $er) use($options) {
                    return $er->queryIn($options['paymentTermsIds']);
                },
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'B2bBundle\Entity\Command',
            'paymentMethodIds' => null,
            'paymentTermsIds' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'b2bbundle_commandrecap';
    }
}