<?php

namespace Glukose\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Repo for the User class
 *
 */
class UserRepository extends EntityRepository
{
    public function findUsersByCreationDate($start, $end)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.created > :start')
            ->andWhere('u.created < :end')
            ->andWhere('u.enabled = 1')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;

    }

}
