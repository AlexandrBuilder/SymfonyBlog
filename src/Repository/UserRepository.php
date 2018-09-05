<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByVerificationToken($verificationToken)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.verificationToken = :verificationToken')
            ->setParameter('verificationToken', $verificationToken)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmail($email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findBySubStrEmail($email)
    {
        $query = $this->createQueryBuilder('u');

        if (strlen($email) != 0) {
            $query
                ->andWhere('u.email LIKE :email')
                ->setParameter('email', $email.'%');
        }

        return $query
            ->setMaxResults(20)
            ->getQuery()
            ->execute();
    }

    public function findAllQuery()
    {
        return $this->createQueryBuilder('u')
            ->getQuery();
    }

    public function countAll()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getParametrsFilterQuery($query, $options)
    {
        if (isset($options['email']) && strlen($options['email']) > 0) {
            $query
                ->andWhere('u.email = :email')
                ->setParameter('email', $options['email']);
        }

        return $query;
    }

    public function findByFilterParametrs($options)
    {
        $query = $this->createQueryBuilder('u');

        return $this->getParametrsFilterQuery($query, $options)->getQuery();
    }

    public function countItemsByFilterParametrs($options)
    {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.id)');

        return $this->getParametrsFilterQuery($query, $options)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
