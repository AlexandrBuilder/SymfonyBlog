<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\Paginator;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends AbstractController
{
    private $router;
    private $paginator;
    private $postRepository;

    public function __construct(UrlGeneratorInterface $router, Paginator $paginator, PostRepository $postRepository)
    {
        $this->router = $router;
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/user/{id}", name="user_show", requirements={"id"="\d+"})
     */
    public function indexAction(User $user)
    {
        if (!$user) {
            throw new NotFoundHttpException('Not exist user');
        }

        if($this->getUser() == $user)
            return $this->redirectToRoute('user_me');

        $paginator = $this
            ->paginator
            ->paginate(
                $this->postRepository->findVerifiedPostsByUserQuery($user),
                $this->postRepository->countVerifiedPostsByUser($user)[1]
            );

        return $this->render('user/show.html.twig', [
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
        $user = $this->getUser();

        $paginator = $this
            ->paginator
            ->paginate(
                $this->postRepository->findPostsByUserQuery($user),
                $this->postRepository->countPostsByUser($user)[1]
            );

        return $this->render('user/me.html.twig', [
            'posts' => $this->paginator->getItems(),
            'paginator' => $this->paginator->getPaginator()
        ]);
    }
}
