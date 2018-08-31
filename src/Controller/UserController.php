<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\Paginator;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends AbstractController
{
    private $router;
    private $paginator;

    public function __construct(UrlGeneratorInterface $router, Paginator $paginator)
    {
        $this->router = $router;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/user/{id}", name="user_show", requirements={"id"="\d+"})
     */
    public function indexAction(User $user, Request $request, PostRepository $postRepository)
    {
        if($this->getUser() == $user)
            return $this->redirectToRoute('user_me');

        $paginator = $this->paginator->paginate($postRepository->findVerifiedPostQuery(),$postRepository->countVerifiedPost()[1]);

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'posts' => $this->paginator->getItems(),
            'paginator' => $this->paginator->getPaginator()
        ]);
    }

    /**
     * @Route("/user/me", name="user_me")
     */
    public function meAction()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
