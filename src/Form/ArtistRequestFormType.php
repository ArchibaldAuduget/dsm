<?php

namespace App\Form;

use App\Entity\ArtistRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArtistRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('artistName', TextType::class, [
                'label' => 'Nom d\'artiste :',
                'attr' => ['placeholder' => 'Entrez votre nom d\'artiste'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un nom d\'artiste',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit faire au moins 2 caractères.'
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Décrivez vous en temps qu\'artiste :',
                'attr' => ['placeholder' => 'Parlez nous de vous en temps qu\'artiste. Ce que vous aimez, vos motivations, quelle genre de musique faîtes vous...'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez écrire une description',
                    ]),
                    new Length([
                        'min' => 60,
                        'minMessage' => 'La description doit faire au minimum 60 caractères'
                    ]),
                ]
            ])
            ->add('demo', TextType::class, [
                'label' => 'Partagez-nous votre musique ! (Optionnel)',
                'attr' => ['placeholder' => 'Envoyez nous un lien vers votre musique.'],
                'required' => false
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions d\'utilisation.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArtistRequest::class,
        ]);
    }
}
