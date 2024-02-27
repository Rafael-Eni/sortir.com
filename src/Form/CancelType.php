<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motif', TextareaType::class, [
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Indiquez le motif de l\'annulation ici',
                    'class' => 'input'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
