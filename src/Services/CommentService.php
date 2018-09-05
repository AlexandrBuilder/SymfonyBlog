<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 30.08.18
 * Time: 17:12
 */

namespace App\Services;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentService
{
    private $user;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        if ($tokenStorage->getToken()) {
            $this->user = $tokenStorage->getToken()->getUser();
        }
    }

    public function create(Comment $comment, Post $post)
    {
        $this->addUserAndPost($comment, $post);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function delete(Comment $comment)
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }

    public function addUserAndPost(Comment $comment, Post $post)
    {
        $comment->setPost($post);
        $comment->setUser($this->user);
        return $comment;
    }

    public function canEditComment()
    {
        return !$this->user->isBlocked();
    }
}
