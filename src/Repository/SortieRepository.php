<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    public function findByFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.inscrits', 'inscrits')
            ->leftJoin('s.organisateur', 'organisateur');

        if (!empty($filters['organisateur'])) {
            $qb->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $filters['organisateur']);
        }
        if (!empty($filters['participant'])) {
            $qb->andWhere('inscrits = :participant')
                ->setParameter('participant', $filters['participant']);
        }
        if (!empty($filters['nonParticipant'])) {
            $qb->andWhere('inscrits != :non_participant')
                ->setParameter('non_participant', $filters['nonParticipant']);
        }
        if (!empty($filters['finished'])) {
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', $filters['finished']);
        }
        if (!empty($filters['search'])) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['site'])) {
            $qb->andWhere('s.site = :site')
                ->setParameter('site', $filters['site']);
        }
        if (!empty($filters['dateDebut'])) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $filters['dateDebut']);
        }
        if (!empty($filters['dateFin'])) {
            $qb->andWhere('s.dateLimiteInscription <= :dateFin')
                ->setParameter('dateFin', $filters['dateFin']);
        }
        return $qb->getQuery()->getResult();
    }

    public function findAllStartingWithinMonth(): array

    {
        $dateLimite = new \DateTime();
        $dateLimite->modify('-1 month');
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut >= :dateLimite')
            ->setParameter('dateLimite', $dateLimite)
            ->getQuery()
            ->getResult();
    }

}

