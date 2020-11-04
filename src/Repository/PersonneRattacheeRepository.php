<?php

namespace App\Repository;

use App\Entity\PersonneRattachee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonneRattachee|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonneRattachee|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonneRattachee[]    findAll()
 * @method PersonneRattachee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRattacheeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonneRattachee::class);
    }

    // /**
    //  * @return PersonneRattachee[] Returns an array of PersonneRattachee objects
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
    public function findOneBySomeField($value): ?PersonneRattachee
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
