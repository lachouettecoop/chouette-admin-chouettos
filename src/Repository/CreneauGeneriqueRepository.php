<?php

namespace App\Repository;

use App\Entity\CreneauGenerique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreneauGenerique|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreneauGenerique|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreneauGenerique[]    findAll()
 * @method CreneauGenerique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreneauGeneriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreneauGenerique::class);
    }

    // /**
    //  * @return CreneauGenerique[] Returns an array of CreneauGenerique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CreneauGenerique
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
