<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 29.08.18
 * Time: 17:42
 */

namespace App\Services;


use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostService
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function addUser(Post $post)
    {
        return $post->setUser($this->user);
    }
}