<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Nom de l\'activité',
                    'class' => 'input'
                ]
            ])
            ->add('dateHeureDebut', DateType::class, [

                'label' => 'Date de l\'activité',
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],

                'attr' => [
                    'placeholder' => 'Date de l\'activité',
                    'class' => 'input'
                ],
            ])
            ->add('duree', TextType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Durée',
                    'class' => 'input'
                ]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite',
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],

                'attr' => [
                    'placeholder' => 'Date limite',
                    'class' => 'input'
                ]
            ])
            ->add('nbInscriptionMax', NumberType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Nombre max. de participants',
                    'class' => 'input'
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Description de la sortie',
                    'class' => 'input'
                ]
            ])
            ->add('lieu', LieuType::class, [
                'label' => 'Adresse du lieu',
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
            ])
            ->add('site', EntityType::class, [
                'label' => false,
                'class' => Site::class,
                'placeholder' => '----- Ecole de rattachement -----',
                'choice_label' => 'nom',
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Publier l\'annonce',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
