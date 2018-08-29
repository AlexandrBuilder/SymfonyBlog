<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Security\Voter\PostVoter;
use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PostController extends Controller
{
    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @Route("/", name="homepage", methods="GET")
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', ['posts' => $postRepository->findVerifiedPost()]);
    }

    /**
     * @Route("/post/new", name="post_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $this->postService->addUser($post);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show", methods="GET")
     */
    public function show(Post $post): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::VIEW, $post);

        return $this->render('post/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/post/{id}/edit", name="post_edit", methods="GET|POST")
     */
    public function edit(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_delete", methods="DELETE")
     */
    public function delete(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('homepage');
    }
}
