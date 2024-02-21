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
                'label' => 'Email :',
                'required' => false,
            ])
            ->add('nom',TextType::class,[
                'label' => 'Nom :',
                'required' => false,
            ])
            ->add('prenom',TextType::class,[
                'label' => 'Prenom :',
                'required' => false,
            ])
            ->add('pseudo',TextType::class,[
                'label' => 'Pseudo :',
                'required' => false,
            ])
            ->add('sexe',TextType::class,[
                'label' => 'Sexe :',
                'required' => false,
            ])
            ->add('telephone',TextType::class,[
                'label' => 'Telephone :',
                'required' => false,
            ])
            ->add('poster_file', FileType::class, [
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
