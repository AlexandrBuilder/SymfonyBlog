<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 02.09.18
 * Time: 22:09
 */

namespace App\Services;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $userRepository;
    private $userPasswordEncoder;
    private $entityManager;
    private $registerEmail;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, EntityManager $entityManager, RegisterEmail $registerEmail)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->registerEmail = $registerEmail;
    }

    public function create(User $user)
    {
        $password = $this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->registerEmail->sendMail($user);
    }
}