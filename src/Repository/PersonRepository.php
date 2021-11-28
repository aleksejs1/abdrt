<?php

namespace App\Repository;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function getUserPersons(User $user)
    {
        $personal = $this
            ->createQueryBuilder('p')
            ->join('p.accesses', 'a')
            ->join('a.user', 'au')
            ->where('au.id = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult()
        ;

        $group = $this
            ->createQueryBuilder('p')
            ->join('p.accesses', 'a')
            ->join('a.accessGroup', 'g')
            ->join('g.users', 'gu')
            ->where('gu.id = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult()
        ;

        return array_merge($personal, $group);
    }
}
