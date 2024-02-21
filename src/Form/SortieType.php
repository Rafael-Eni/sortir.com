<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'activité',
            ])
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax', TextType::class, [
                'label' => 'Nombre maximum de participant'
            ])
            ->add('infosSortie', TextType::class, [
                'label' => 'Description de la sortie'
            ])
            ->add('lieu', LieuType::class, [
                'label' => 'Lieu de l\'activité'
            ])
            ->add('site', EntityType::class, [
                'label' => 'Ecole de rattachement',
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => '----- Choisir un site -----',
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
