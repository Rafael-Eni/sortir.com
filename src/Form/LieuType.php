<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner le lieu',
                    ])
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Lieu de la sortie',
                    'class' => 'input'
                ]
            ])
            ->add('rue', TextType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre prénom',
                    ])
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Rue',
                    'class' => 'input'
                ]
            ])
            ->add('ville', VilleType::class, [
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
