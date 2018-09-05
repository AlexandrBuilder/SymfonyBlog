<?php

namespace App\Repository;

use App\Entity\Assessment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Assessment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assessment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assessment[]    findAll()
 * @method Assessment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssessmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Assessment::class);
    }

    public function findByUserAndPost(Post $post, User $user)
    {
        return $this->createQueryBuilder('a')
            ->join('a.post', 'p')
            ->join('a.user', 'u')
            ->where('p = :post')
            ->andWhere('u = :user')
            ->setParameter('post', $post)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
