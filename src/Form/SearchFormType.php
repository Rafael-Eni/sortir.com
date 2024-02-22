<?php

namespace App\Form;

use App\Entity\Site;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Sodium\add;

class SearchFormType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $this->security->getUser()->getSexe();

        $libelleOrganisateur = $user === 'femme' ? 'Sorties dont je suis l\'organisatrice' : 'Sorties dont je suis l\'organisateur';
        $libelleParticipant = $user === 'femme' ? 'Sorties auxquelles je suis inscrite' : 'Sorties auxquelles je suis inscrit';
        $libelleNonParticipant = $user === 'femme' ? 'Sorties auxquelles je ne suis pas inscrite' : 'Sorties auxquelles je ne suis pas inscrit';


        $builder
            ->add('site', EntityType::class, [
                'required' => false,
                'label' => 'Ecole de rattachement',
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => '----- Choisir un site -----',
                'row_attr' => [
                    'class' => 'wave-group bar width'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('search', SearchType::class, [
                'required' => false,
                'label' => 'Rechercher par mot clé',
                'row_attr' => [
                    'class' => 'wave-group bar width'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
                'attr' => [
                    'placeholder' => 'Recherche',
                    'class' => 'input'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre le',
                'required' => false,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'class' => 'input'
                    ],
                'row_attr' => [
                    'class' => 'wave-group bar width'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et le',
                'required' => false,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'class' => 'input'
                ],
                'row_attr' => [
                    'class' => 'wave-group bar width'// Remplacez ici la classe Bootstrap par votre classe CSS personnalisée
                ],
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => $libelleOrganisateur
            ])
            ->add('participant', CheckboxType::class, [
                'required' => false,
                'label' => $libelleParticipant
            ])
            ->add('nonParticipant', CheckboxType::class, [
                'required' => false,
                'label' => $libelleNonParticipant
            ])
            ->add('finished', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées'
            ])
        ;
    }

}