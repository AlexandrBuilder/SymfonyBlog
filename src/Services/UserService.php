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
use LogicException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $userRepository;
    private $userPasswordEncoder;
    private $entityManager;
    private $emailService;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManager $entityManager,
        EmailService $emailService
    ) {
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

    public function changePassword(User $user)
    {
        $password = $this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $user->removeTokenUser();

        $this->entityManager->flush();
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

        if (empty($user)) {
            throw new BadRequestHttpException("User not find");
        }

        $user->activateUser();
        $this->entityManager->flush();

        return $user;
    }

    public function getToken($user)
    {
        if (empty($user)) {
            throw new BadRequestHttpException("User not find");
        }

        $user->getNewVerificationToken();

        $this->entityManager->flush();

        $this->sendChangePasswordMail($user);
    }

    public function sendChangePasswordMail(User $user)
    {
        $renderOptions = [
            'template' => 'emails/change_password.html.twig',
            'options' => ['user' => $user]
        ];

        $this->emailService->sendMail($user, 'Change password', $renderOptions, EmailService::CONST_TEXT_HTML);
    }

    public function getArrayUsers($emailSubStr)
    {
        $users = $this->userRepository->findBySubStrEmail($emailSubStr);

        $usersArray = [];

        foreach ($users as $user) {
            $usersArray[] = $user->getArrayUsers();
        }

        return $usersArray;
    }

    public function setBlockStatusUser(User $user)
    {
        if ($user->isAdmin()) {
            throw new LogicException('Admin can not be blocked');
        }
        if ($user->isNotVerifiedUser()) {
            throw new LogicException('No verified user can not be blocked');
        }
        if ($user->isBlockedUser()) {
            throw new LogicException('User blocked already');
        }
        $user->setBlockStatusUser();
        $this->entityManager->flush($user);
    }

    public function setActiveStatusUser(User $user)
    {
        if ($user->isActivateStatusUser()) {
            throw new LogicException('User activate already');
        }
        $user->setActiveStatusUser();
        $this->entityManager->flush($user);
    }

    private function cmp($userOne, $userTwo)
    {
        return $userOne['rating'] < $userTwo['rating'];
    }

    public function getTopFiveUser()
    {
        $users=$this->userRepository->findAll();

        $userRating=[];

        foreach ($users as $key => $user) {
            $userRating[$key]['user'] = $user;
            $userRating[$key]['rating'] = $user->getRating();
        }

        usort($userRating, array(UserService::class,'cmp'));

        return array_slice($userRating, 0, 5);
    }
}
