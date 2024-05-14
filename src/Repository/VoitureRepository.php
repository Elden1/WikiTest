<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    /**
     * Retrieves all Voiture entities.
     *
     * @return Voiture[] Returns an array of Voiture objects
     */
    public function findAllVoitures(): array
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->getResult();
    }

    /*le filtre pour dates et prix max */
    public function findVoituresDisponiblesEntreDates(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin, $prixMax): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere(':dateDebut BETWEEN v.dateDebut AND v.dateFin')
            ->andWhere(':dateFin BETWEEN v.dateDebut AND v.dateFin')
            ->AndWhere ('v.prix <= :prixMax ')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('prixMax', $prixMax)
            ->getQuery()
            ->getResult();
    }
}