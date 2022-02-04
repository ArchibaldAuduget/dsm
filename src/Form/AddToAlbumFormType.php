<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Music;
use App\Repository\MusicRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddToAlbumFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('music', EntityType::class, [
                'placeholder' => '-- choisir un album --',
                'multiple' => true,
                'expanded' => true,
                'constraints' => [ new Count ([
                    'min' => 1,
                    'minMessage' => 'Vous devez ajouter au moins une musique',
                ])],
                'class' => Music::class,
                'choice_label' => function ($music) {
                    if ( $music->getAlbum() !== null) {return false;}
                    return $music->getName();
                }
            ])
        ;
    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Music::class,
    //     ]);
    // }
}
