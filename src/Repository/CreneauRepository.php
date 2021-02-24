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
        /** @var \DateTime $dateC */
        $dateC = clone $date;
        $dateC->setTime($heureDebut->format('H'),$heureDebut->format('i'),$heureDebut->format('s')  );

        return $this->createQueryBuilder('c')
            ->leftJoin('c.creneauGenerique', 'cg')
            ->andWhere('cg.id = :val')
            ->andWhere('c.debut = :date')
            ->setParameter('val', $idCG)
            ->setParameter('date', $dateC)
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
