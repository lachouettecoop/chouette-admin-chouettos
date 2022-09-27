<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param \DateTime $date
     * @return int|mixed|string
     */
    public function findByDateDebutPiaf($date)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('DATE_DIFF(:date, u.dateDebutPiaf) != 0')
            ->andWhere('MOD(DATE_DIFF(:date, u.dateDebutPiaf), 28) = 0')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findForWarningMail()
    {
        return $this->createQueryBuilder('u')
            ->andWhere("u.statut = 'chouette en alerte'")
            ->andWhere("u.absenceLongueDureeCourses != 1")
            ->andWhere("u.absenceLongueDureeSansCourses != 1")
            ->andWhere("u.attenteCommissionParticipation != 1")
            ->getQuery()
            ->getResult()
            ;
    }

    public function findForAbsenceLongueDureeCourses()
    {
        return $this->createQueryBuilder('u')
            ->andWhere("u.absenceLongueDureeCourses = 1")
            ->andWhere("u.dispenseDefinitive != 1")
            ->getQuery()
            ->getResult()
            ;
    }


}
