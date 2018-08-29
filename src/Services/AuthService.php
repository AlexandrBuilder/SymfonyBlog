<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 28.08.18
 * Time: 18:01
 */

namespace App\Services;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class AuthService
{
    private $repositoryUser;
    private $entityManager;

    public function __construct(UserRepository $repositoryUser,EntityManager $entityManager)
    {
        $this->repositoryUser = $repositoryUser;
        $this->entityManager = $entityManager;
    }

    public function activateUser($user) {
        $user=$user->setVerificationToken('');
//        $this->entityManager->persist($user);
//        $this->entityManager->flush();
    }
}