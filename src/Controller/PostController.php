<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Helpers\Paginator;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Security\Voter\CommentVoter;
use App\Security\Voter\PostVoter;
use App\Services\CommentService;
use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PostController extends Controller
{
    private $postService;
    private $router;
    private $commentService;
    private $paginator;

    public function __construct(PostService $postService, CommentService $commentService, UrlGeneratorInterface $router,Paginator $paginator)
    {
        $this->postService = $postService;
        $this->router = $router;
        $this->commentService = $commentService;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="homepage", methods="GET")
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $paginator = $this
            ->paginator
            ->paginate(
                $postRepository->findVerifiedPostQuery(),
                $postRepository->countVerifiedPost()[1]
            );

        return $this->render('post/index.html.twig', [
            'posts' => $this->paginator->getItems(),
            'paginator' => $this->paginator->getPaginator()
        ]);
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

            $this->postService->create($post);

            $this->addFlash(
                'success_post',
                'Post added successfully'
            );

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show", methods="GET|POST")
     */
    public function show(Request $request, Post $post, CommentRepository $commentRepository): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::VIEW, $post);

        $commentsForm = $this->addComment($request, $post);
        if($commentsForm === true)
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments_form' => $commentsForm,
        ]);
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

            $this->getDoctrine()
                ->getManager()
                ->flush();

            $this->addFlash(
                'success_post',
                'Post edited successfully'
            );

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
            $this->postService->delete($post);
        }

        $this->addFlash(
            'success_post',
            'You deleted your article "'.$post->getTitle().'"'
        );

        return $this->redirectToRoute('user_me');
    }

    public function addComment(Request $request, Post $post)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment);
            $this->commentService->create($comment, $post);

            $this->addFlash(
                'success_comment',
                'Comment added successfully'
            );

            return true;
        }

        return $this->render('comment/_form.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/published/{id}", name="post_published")
     */
    public function publishedAction(Request $request, Post $post)
    {
        $this->postService->published($post);

        $this->addFlash(
            'success_post',
            'You published your article "'.$post->getTitle().'"'
        );

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
