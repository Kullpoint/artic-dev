<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationPartnerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a email',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email'
                    ]),
                    new Length([
                        'min' => 5,
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                    ]),
                ],
            ])
            ->add('birthday', BirthdayType::class, [
                'required' => true,
                'constraints' => [new NotBlank(),],
            ])
            ->add('phone', TextType::class, [
                'required' => true,
                'attr' => [
                    'maxlength' => 10
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 10,
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
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
