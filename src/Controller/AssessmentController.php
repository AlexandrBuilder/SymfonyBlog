<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Services\AssessmentService;
use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/assessment")
 */
class AssessmentController extends AbstractController
{
    private $assessmentService;
    private $postService;

    public function __construct(AssessmentService $assessmentService, PostService $postService)
    {
        $this->assessmentService = $assessmentService;
        $this->postService = $postService;
    }
    /**
     * @Route("/new", name="new_assessment")
     * @param Request $request
     * @param PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, PostRepository $postRepository)
    {
        $post = $postRepository->find($request->request->get('post_id'));
        $assessment = $request->request->get('assessment');

        if (!$post) {
            throw new BadRequestHttpException('This post not exist');
        }
        if (!$assessment) {
            throw new BadRequestHttpException('Assessment post not exist');
        }

        $assessment = $this->assessmentService->createForUser($assessment, $post);

        return $this->render('assessment/_new.html.twig', [
            'assessment' => $assessment,
            'rating' => $post->getRatingPost(),
            'post' => $post
        ]);
    }

    /**
     * @Route("/delete", name="delete_assessment")
     * @param Request $request
     * @param PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, PostRepository $postRepository)
    {
        $post = $postRepository->find($request->request->get('post_id'));
        if (!$post) {
            throw new BadRequestHttpException('This post not exist');
        }

        $this->assessmentService->deleteForUser($post);

        return $this->render('assessment/_delete.html.twig', [
            'rating' => $post->getRatingPost(),
            'post' => $post
        ]);
    }
}
