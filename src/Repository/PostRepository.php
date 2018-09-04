<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllQuery()
    {
        return $this->createQueryBuilder('p')
            ->getQuery();
    }

    public function countAll()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countVerifiedPost()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.status = :status')
            ->setParameter('status', Post::CONST_PUBLISHED)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findVerifiedPostQuery()
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', Post::CONST_PUBLISHED)
            ->orderBy('p.publicationDate', 'DESC')
            ->getQuery();
    }

    public function countPostsByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->join('p.user', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPostsByUserQuery(User $user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->orderBy('p.publicationDate', 'DESC')
            ->getQuery();
    }

    public function countVerifiedPostsByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->join('p.user', 'u')
            ->where('u = :user')
            ->andWhere('p.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Post::CONST_PUBLISHED)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findVerifiedPostsByUserQuery(User $user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u = :user')
            ->andWhere('p.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Post::CONST_PUBLISHED)
            ->orderBy('p.publicationDate', 'DESC')
            ->getQuery();
    }

    private function getParametrsFilterQuery($query, $options)
    {
        if (isset($options['email']) && strlen($options['email']) > 0) {
            $query
                ->andWhere('u.email = :email')
                ->setParameter('email', $options['email']);
        }

        if (isset($options['status']) && strlen($options['status']) > 0) {
            $query
                ->andWhere('p.status = :status')
                ->setParameter('status', $options['status']);
        }

        if (isset($options['date_from'])) {
            $query
                ->andWhere('p.publicationDate > :publicationDatFrom')
                ->setParameter('publicationDatFrom', $options['date_from']);
        }

        if (isset($options['date_to'])) {
            $query
                ->andWhere('p.publicationDate < :publicationDateTo')
                ->setParameter('publicationDateTo', $options['date_to']);
        }

        return $query;
    }

    public function findByFilterParametrs($options)
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.user', 'u');

        return $this->getParametrsFilterQuery($query, $options)->getQuery();
    }

    public function countItemsByFilterParametrs($options)
    {
        $query = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->join('p.user', 'u');

        return $this->getParametrsFilterQuery($query, $options)
            ->getQuery()
            ->getOneOrNullResult();
    }


//    /**
//     * @return Post[] Returns an array of Post objects
//     */
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
    public function findOneBySomeField($value): ?Post
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
