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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $userRepository;
    private $userPasswordEncoder;
    private $entityManager;
    private $emailService;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, EntityManager $entityManager, EmailService $emailService)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
    }

    public function create(User $user)
    {
        $password = $this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendConfimMail($user);
    }

    public function sendConfimMail(User $user)
    {
        $renderOptions = [
            'template' => 'emails/registration.html.twig',
            'options' => ['user' => $user]
        ];

        $this->emailService->sendMail($user, 'Confirmation letter', $renderOptions, EmailService::CONST_TEXT_HTML);
    }

    public function activateUser(string $verificationToken)
    {
        $user = $this->userRepository->findByVerificationToken($verificationToken);

        if(empty($user)) {
            throw new BadRequestHttpException("User not find");
        }

        $user = $user->setVerificationToken('');
        $this->entityManager->flush();

        return $user;
    }

    public function getArrayUsers($emailSubStr)
    {
        $users = $this->userRepository->findBySubStrEmail($emailSubStr);
        $usersArray = [];
        foreach ($users as $user){
            $usersArray[] = $user->getArrayUsers();
        }
        return $usersArray;
    }

}