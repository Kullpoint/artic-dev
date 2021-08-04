<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationPerformerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', FileType::class, [
                'required' => false,
            ])
            ->add('specialization', EntityType::class, [
                'required' => true,
                'multiple' => true,
                'class' => Category::class,
                'choice_label' => 'topic',
            ])
            ->add('experience', TextType::class, [
                'label' => 'Experience(in years)',
                'required' => true,
                'attr' => [
                    'maxlength' => 2
                ],
            ])
            ->add('birthday', BirthdayType::class, [
                'required' => true,
            ])
            ->add('summeryTypes', ChoiceType::class, [
                'required' => true,
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
            ->add('agree', CheckboxType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('sendEmail', CheckboxType::class, [
                'required' => false,
            ]);
    }
}
