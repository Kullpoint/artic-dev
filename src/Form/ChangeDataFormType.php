<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeDataFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['data']['user'];

        $builder
            ->add('lastname', TextType::class, [
                'data' => $user->getLastname(),
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'data' => $user->getFirstname(),
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'data' => $user->getEmail(),
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
            ->add('phone', TextType::class, [
                'data' => $user->getPhone(),
                'attr' => [
                    'maxlength' => 10
                ],
                'constraints' => [
                    new Length(['min' => 10, 'max' => 10])
                ]
            ]);

        if ($options['data']['user']->getClient()) {
            $builder
                ->add('country', CountryType::class, [
                    'data' => $user->getClient()->getCountry(),
                ])
                ->add('city', TextType::class, [
                    'data' => $user->getClient()->getCity(),
                ]);
        } elseif ($user->getPerformer()) {
            $builder
                ->add('birthday', TextType::class, [
                    'required' => true,
                    'data' => $user->getPerformer()->getBirthday()->format('d.m.Y'),
                ])
                ->add('country', CountryType::class, [
                    'data' => $user->getPerformer()->getCountry(),
                ])
                ->add('city', TextType::class, [
                    'data' => $user->getPerformer()->getCity(),
                ]);
        } elseif ($user->getPartner()) {
            $builder
                ->add('birthday', TextType::class, [
                    'required' => true,
                    'data' => $user->getPartner()->getBirthday()->format('d.m.Y'),
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
