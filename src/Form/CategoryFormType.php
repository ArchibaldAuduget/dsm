<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => ['placeholder' => 'Entrez le nom de la catégorie'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer une catégorie',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'La catégorie doit faire au moins 3 caractères.'
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
