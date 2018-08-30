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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentService
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        if($tokenStorage->getToken())
            $this->user = $tokenStorage->getToken()->getUser();
    }

    public function addUserAndPost(Comment $comment, Post $post)
    {
        $comment->setPost($post);
        $comment->setUser($this->user);
        return $comment;
    }

}