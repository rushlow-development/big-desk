<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'empty_data' => fn (FormInterface $form): Note => new Note(
                $form->get('title')->getData(), // @phpstan-ignore-line
                $form->get('content')->getData(), // @phpstan-ignore-line
            ),
        ]);
    }
}
