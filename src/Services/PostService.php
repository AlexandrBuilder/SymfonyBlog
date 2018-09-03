<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 29.08.18
 * Time: 17:42
 */

namespace App\Services;


use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostService
{
    private $user;
    private $entityManager;
    private $assessmentService;
    private $commentService;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager, AssessmentService $assessmentService, CommentService $commentService)
    {
        $this->entityManager = $entityManager;
        if($tokenStorage->getToken())
            $this->user = $tokenStorage->getToken()->getUser();

        $this->assessmentService = $assessmentService;
        $this->commentService = $commentService;
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

    public function isHostPost(Post $post) {
        return $this->user == $post->getUser();
    }

    public function haveUserRoleAdmin() {
        return in_array("ROLE_ADMIN", $this->user->getRoles());
    }

    public function canViewPost(Post $post)
    {
        if ($post->isVerified()) {
            return true;
        }
        if ($this->isHostPost($post) || $this->haveUserRoleAdmin()) {
            return true;
        }
        return false;
    }

    public function canEditPost(Post $post) {
        if ($this->isHostPost($post) && $post->isEditMode()) {
            return true;
        }
        return false;
    }

    public function canDeletePost(Post $post) {
        return $this->isHostPost($post);
    }

}