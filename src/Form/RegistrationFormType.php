<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre pseudo',
                    ])
                ],
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
            ->add('email', TextType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre e-mail',
                    ]),
                ],
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
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'label_attr' => [
                    'class' => 'label'
                ],
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'class' => 'input'
                ]
            ])
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre nom',
                    ])
                ],
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
            ->add('prenom', TextType::class, [
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
                    'placeholder' => 'Prénom',
                    'class' => 'input'
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'placeholder' => 'Sexe',
                'choices' => [
                    'Feminin' => 'femme',
                    'Masculin' => 'homme'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre sexe',
                    ])
                ],
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
            ->add('telephone', TextType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre numéro de téléphone',
                    ]),
                ],
                'row_attr' => [
                    'class' => 'wave-group bar'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'input'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes et conditions',
                    ]),
                ],
                'label' => 'Accepter les termes & conditions',
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
            'data_class' => Participant::class,
        ]);
    }
}
