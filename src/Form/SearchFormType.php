<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'required' => false,
                'label' => 'Ecole de rattachement',
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => '----- Choisir un site -----',
            ])
            ->add('search', SearchType::class, [
                'label' => 'Rechercher',
                'required' => false
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre',
                'required' => false,
                'attr' => ['min' => (new \DateTime())->format('Y-m-d')]
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et',
                'required' => false,
                'attr' => ['min' => (new \DateTime())->format('Y-m-d')]
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur.rice'
            ])
            ->add('participant', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit.es'
            ])
            ->add('nonParticipant', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit.es'
            ])
            ->add('finished', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées'
            ])
            ->add('submit', SubmitType::class);
        ;
    }

}