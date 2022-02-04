<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AlbumFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'album :',
                'attr' => ['placeholder' => 'Entrez le nom de l\'album'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer le nom de l\'album',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom de l\'album doit faire au moins 2 caractères.'
                    ]),
                ]
            ])
            ->add('img', FileType::class, [
                'label' => 'Ajouter une photo',
                'data_class' => null,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être de type JPG ou PNG',
                    ])
                ],
            ])
            ->add('category', EntityType::class, [
                'multiple' => true,
                'label' => 'Catégorie',
                'expanded' => true,
                'constraints' => [ new Count ([
                    'min' => 1,
                    'max' => 3,
                    'minMessage' => 'Vous devez spécifier au moins une catégorie',
                    'maxMessage' => 'Vous ne pouvez spécifier que 3 catégories maximum'
                ])],
                'placeholder' => '-- choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getCategory());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}