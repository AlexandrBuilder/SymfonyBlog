<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AppCustomAuthenticator extends AbstractGuardAuthenticator
{
    private $router;
    private $repositoryUser;

    public function __construct(UserRepository $repositoryUser, UrlGeneratorInterface $router)
    {
        $this->repositoryUser = $repositoryUser;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == $this->router->generate('login') && $request->getMethod() == 'POST';
    }

    public function getCredentials(Request $request)
    {
        return [
            'email' => $request->request->get('email'),
            'password'=>$request->get('password')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!strlen($credentials['email'])) {
            throw new AuthenticationException('Email is empty');
        }
        if (!strlen($credentials['password'])) {
            throw new AuthenticationException('Password is empty');
        }
        $user = $this->repositoryUser->findOneByEmail([$credentials['email']]);
        if (!isset($user)) {
            throw new AuthenticationException('This account not exist');
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user->isActivateStatusUser()) {
            throw new AuthenticationException('Account not verified');
        }
        if (!password_verify($credentials['password'], $user->getPassword())) {
            throw new AuthenticationException('Invalid user data');
        }
        if ($user->isBlocked()) {
            throw new AuthenticationException('This account is blocked');
        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($request->getPathInfo() === $this->router->generate('login')) {
            $request->getSession()->set('username', $request->request->get('username'));

            return new RedirectResponse('/');
        }
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse('/login');
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
