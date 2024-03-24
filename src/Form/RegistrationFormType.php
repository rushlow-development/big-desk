<?php

namespace App\Form;

use App\Form\Dto\UserObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('displayName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a display name',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your username should be at least {{ limit }} characters',
                        'max' => 180,
                    ]),
                ],
            ])
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a username',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your username should be at least {{ limit }} characters',
                        'max' => 180,
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserObject::class,
        ]);
    }
}
