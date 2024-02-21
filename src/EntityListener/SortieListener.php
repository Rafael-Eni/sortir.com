<?php

namespace App\EntityListener;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortieListener
{

    public function __construct(private readonly EtatRepository $etatRepository, private readonly EntityManagerInterface $em)
    {
    }

    public function postLoad(Sortie $sortie): void
    {
        if ($sortie->getDateHeureDebut() < new \DateTime()) {
            $etat = $this->etatRepository->findOneBy(['libelle' => 'Passée']);
            $sortie->setEtat($etat);
            $this->em->persist($sortie);
            $this->em->flush();
        }
    }
}