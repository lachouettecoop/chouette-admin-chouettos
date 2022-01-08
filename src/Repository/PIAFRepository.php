<?php

namespace App\Repository;

use App\Entity\PIAF;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PIAF|null find($id, $lockMode = null, $lockVersion = null)
 * @method PIAF|null findOneBy(array $criteria, array $orderBy = null)
 * @method PIAF[]    findAll()
 * @method PIAF[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PIAFRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PIAF::class);
    }

    public function findPIAFaComptabiliser()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.comptabilise is null')
            ->andWhere('p.pourvu = 1')
            ->andWhere('p.non_pourvu = 0')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return PIAF[] Returns an array of PIAF objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PIAF
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
