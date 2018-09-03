<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\Paginator;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends AbstractController
{
    private $router;
    private $paginator;
    private $postRepository;
    private $userRepository;
    private $userService;

    public function __construct(UrlGeneratorInterface $router, Paginator $paginator, PostRepository $postRepository, UserRepository $userRepository, UserService $userService)
    {
        $this->router = $router;
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
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

    /**
     * @Route("/user/json", name="user_json")
     */
    public function jsonUser(Request $request): JsonResponse
    {
        $emailSubStr = $request->request->get('sub_str') ?? '';

        return new JsonResponse($this->userService->getArrayUsers($emailSubStr));
    }
}
