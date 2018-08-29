<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 27.08.18
 * Time: 16:27
 */

namespace App\Controller;

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use App\Services\RegisterEmail;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AuthController  extends AbstractController
{
    private $registerEmail;

    public function __construct(RegisterEmail $registerEmail)
    {
        $this->registerEmail = $registerEmail;
    }

    /**
     * @Route("/register", name="registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->registerEmail->sendMail($user);

            return $this->redirectToRoute('registration');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView()]
        );
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {

    }

    /**
     * @Route("/verification/{verificationToken}", name="verification")
     */
    public function verificationAction($verificationToken) {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findByVerificationToken($verificationToken);
        if(empty($user)) {
            throw new BadRequestHttpException("User not find");
        }
        $user = $user[0];
        return $this->render('auth/success_register.html.twig', [
            'user' => $user
        ]);
    }

}