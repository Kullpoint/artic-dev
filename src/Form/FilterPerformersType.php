<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterPerformersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('experience', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => [
                    'To 1' => '1',
                    '1-2' => '1-2',
                    '2-5' => '2-5',
                    '5-10' => '5-10',
                    'From 10' => '10',
                ],
            ])
            ->add('summeryTypes', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => [
                    'Term Paper' => 'Term Paper',
                    'Graduate work' => 'Graduate work',
                    'Summery' => 'Summery',
                    'Research' => 'Research',
                    'Essay' => 'Essay',
                    'Article' => 'Article',
                    'Technical writing' => 'Technical writing',
                    'Scientific articles' => 'Scientific articles',
                ],
            ])
            ->add('rating', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => [
                    '0-1' => '0-1',
                    '1-2' => '1-2',
                    '2-3' => '2-3',
                    '3-4' => '3-4',
                    '4-5' => '4-5',
                ],
            ])
            ->add('topic', EntityType::class, [
                'required' => true,
                'multiple' => true,
                'class' => Category::class,
                'data' => [$options['data']['category']] ?? null,
                'choice_label' => 'topic',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
