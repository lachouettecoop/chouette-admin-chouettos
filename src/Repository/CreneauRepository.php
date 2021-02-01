<?php

namespace App\Repository;

use App\Entity\Creneau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Creneau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creneau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creneau[]    findAll()
 * @method Creneau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreneauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Creneau::class);
    }

    // /**
    //  * @return Creneau[] Returns an array of Creneau objects
    //  */

    public function findByCreneauGenerique($idCG, \DateTimeInterface $date, \DateTimeInterface $heureDebut)
    {

        return $this->createQueryBuilder('c')
            ->leftJoin('c.creneauGenerique', 'cg')
            ->andWhere('cg.id = :val')
            ->andWhere('c.date = :date')
            ->andWhere('c.heureDebut = :heure')
            ->setParameter('val', $idCG)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('heure', $heureDebut->format('H:i:s'))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Creneau
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
