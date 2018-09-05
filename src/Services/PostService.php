<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 29.08.18
 * Time: 17:42
 */

namespace App\Services;

use App\Entity\Post;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostService
{
    private $user;
    private $entityManager;
    private $assessmentService;
    private $commentService;
    private $postRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManager $entityManager,
        AssessmentService $assessmentService,
        CommentService $commentService,
        PostRepository $postRepository
    ) {
        $this->entityManager = $entityManager;
        if ($tokenStorage->getToken()) {
            $this->user = $tokenStorage->getToken()->getUser();
        }

        $this->assessmentService = $assessmentService;
        $this->commentService = $commentService;
        $this->postRepository = $postRepository;
    }

    public function create(Post $post)
    {
        $post->setUser($this->user);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function published(Post $post)
    {
        if (!$post->isDraftStatus()) {
            throw new \LogicException('This post did not have editing status!');
        }
        $post->moderated();
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function delete(Post $post)
    {
        foreach ($post->getComments() as $comment) {
            $this->commentService->delete($comment);
        }
        foreach ($post->getAssessments() as $assessment) {
            $this->assessmentService->delete($assessment);
        }
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    public function isHostPost(Post $post)
    {
        return $this->user == $post->getUser();
    }

    public function canViewPost(Post $post)
    {
        if ($post->isVerified()) {
            return true;
        }

        if (is_object($this->user) && (($this->isHostPost($post) && !($this->user->isBlocked()))
                || $this->user->isAdmin())) {
            return true;
        }

        return false;
    }

    public function canEditPost(Post $post)
    {
        if (($this->isHostPost($post) && $post->isEditMode() && !($this->user->isBlocked()))
            || $this->user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function canDeletePost(Post $post)
    {
        return $this->isHostPost($post) && !($this->user->isBlocked());
    }

    private function cmp($userOne, $userTwo)
    {
        return $userOne['rating'] < $userTwo['rating'];
    }

    public function getTopFivePost()
    {
        $posts=$this->postRepository->findVerifiedPost();

        $postRating=[];

        foreach ($posts as $key => $post) {
            $postRating[$key]['post'] = $post;
            $postRating[$key]['rating'] = $post->getRatingPost();
        }

        usort($postRating, [PostService::class,'cmp']);

        return array_slice($postRating, 0, 5);
    }
}
