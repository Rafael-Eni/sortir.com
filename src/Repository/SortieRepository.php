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
                ->setParameter('organisateur', $filters['organisateur'])
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['participant'])) {
            $qb->andWhere('inscrits = :participant')
                ->setParameter('participant', $filters['participant'])
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['nonParticipant'])) {
            $subQueryBuilder = $this->createQueryBuilder('sub')
                ->select('sub.id')
                ->leftJoin('sub.inscrits', 'sub_inscrits')
                ->andWhere('sub_inscrits.id = :nonParticipant');

            $qb->andWhere($qb->expr()->notIn('s.id', $subQueryBuilder->getDQL()))
                ->setParameter('nonParticipant', $filters['nonParticipant'])
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['finished'])) {
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', $filters['finished']);
        }
        if (!empty($filters['search'])) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $filters['search'] . '%')
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['site'])) {
            $qb->andWhere('s.site = :site')
                ->setParameter('site', $filters['site'])
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['dateDebut'])) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $filters['dateDebut'])
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['dateFin'])) {
            $qb->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $filters['dateFin'])
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 2);
        }
        if (!empty($filters['created'])) {
            $qb->andWhere('s.etat = :created')
                ->setParameter('created', $filters['created']);
        }
        return $qb->getQuery()->getResult();
    }

    public function findAllStartingWithinMonth(): array

    {
        $dateLimite = new \DateTime();
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut >= :dateLimite')
            ->andWhere('s.etat != 6')
            ->andWhere('s.etat != 1')
            ->andWhere('s.etat = :etat')
            ->setParameter('etat', 2)
            ->setParameter('dateLimite', $dateLimite)
            ->getQuery()
            ->getResult();
    }

    public function findUserInscrit(int $id): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.inscrits', 'inscrits')
            ->andWhere('inscrits = :participant')
            ->setParameter('participant', $id);
        return $qb->getQuery()->getResult();

    }
}

