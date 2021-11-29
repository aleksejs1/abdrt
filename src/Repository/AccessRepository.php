<?php

namespace App\Repository;

use App\Entity\Access;
use App\Entity\Person;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Access|null find($id, $lockMode = null, $lockVersion = null)
 * @method Access|null findOneBy(array $criteria, array $orderBy = null)
 * @method Access[]    findAll()
 * @method Access[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Access::class);
    }

    public function hasAccess(User $user, Person $person): bool
    {
        $access = $this
            ->createQueryBuilder('a')
            ->join('a.person', 'p')
            ->where('p.id = :person')
            ->setParameter('person', $person->getId())
            ->leftJoin('a.user', 'au')
            ->andWhere('au.id = :user')
            ->setParameter('user', $user->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($access) {
            return true;
        }

        $access = $this
            ->createQueryBuilder('a')
            ->join('a.person', 'p')
            ->where('p.id = :person')
            ->setParameter('user', $user->getId())
            ->leftJoin('a.accessGroup', 'g')
            ->leftJoin('g.users', 'gu')
            ->setParameter('person', $person->getId())
            ->andWhere('gu.id = :user')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($access) {
            return true;
        }

        return false;
    }
}
