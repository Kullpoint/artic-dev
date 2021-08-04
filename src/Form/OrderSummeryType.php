<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderSummeryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summeryType', TextType::class, [
                'required' => true,
            ])
            ->add('theme', TextType::class, [
                'required' => true,
            ])
            ->add('category', ChoiceType::class, [
                'required' => true,
                'choices' => $options['data']['categories'],
                'choice_label' => 'topic'
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
            ])
            ->add('requirements', TextType::class, [
                'required' => false,
            ])
            ->add('priceFrom', TextType::class, [
                'label' => 'Range price',
                'required' => true,
            ])
            ->add('priceTo', TextType::class, [
                'required' => true,
            ])
            ->add('deadline', DateType::class, [
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
