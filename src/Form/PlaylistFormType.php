<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlaylistFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nouvelle playlist :',
                'attr' => ['placeholder' => 'Entrez le nom de votre playlist'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer le nom de la playlist',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom de la playlist doit faire au moins 2 caractères.'
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
