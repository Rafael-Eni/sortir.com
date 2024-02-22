<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',TextType::class,[
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'E-mail',
                    'class' => 'input'
                ]
            ])
            ->add('nom',TextType::class,[
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'input'
                ]
            ])
            ->add('prenom',TextType::class,[
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'input'
                ]
            ])
            ->add('pseudo',TextType::class,[
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Pseudo',
                    'class' => 'input'
                ]
            ])
            ->add('sexe',TextType::class,[
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Sexe',
                    'class' => 'input'
                ]
            ])
            ->add('telephone',TextType::class,[
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'input'
                ]
            ])
            ->add('poster_file', FileType::class, [
                'label' => false,
                'required' => false,
                'mapped'=> false,
                'constraints'=> [
                    new File([
                        'maxSize'=>'1024k',
                        'mimeTypes'=>[
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage'=>'Ce format n\'est pas ok',
                        'maxSizeMessage'=>'Ce fichier est trop lourd'
                    ])
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
