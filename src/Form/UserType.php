<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', SearchType::class, [
                'label' => false,
                'required' => false,
                'label_attr' => [
                    'class' => 'termes'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Recherche'
                ]
            ])
            ->add('actif', CheckboxType::class, [
                'required' => false,
                'label' => 'Utilisateur actif',
                'label_attr' => [
                    'class' => 'termes'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
            ])
            ->add('desactiver', CheckboxType::class, [
                'required' => false,
                'label' => 'Utilisateur desactivé',
                'label_attr' => [
                    'class' => 'termes'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
