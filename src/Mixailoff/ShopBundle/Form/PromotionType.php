<?php

namespace Mixailoff\ShopBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('percent', IntegerType::class, array(
                'attr' => array('min' => 1, 'max' => 100)
            ))
            ->add('startDate')
            ->add('endDate')
            ->add('category', EntityType::class, array(
                'class' => 'Mixailoff\ShopBundle\Entity\ProductCategory',
                'choice_label' => 'name',
                'required' => false))
            ->add('product', EntityType::class, array(
                'class' => 'Mixailoff\ShopBundle\Entity\Product',
                'choice_label' => 'title',
                'required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mixailoff\ShopBundle\Entity\Promotion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mixailoff_shopbundle_promotion';
    }
}
