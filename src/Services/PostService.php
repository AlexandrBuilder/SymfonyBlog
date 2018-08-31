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
        if($tokenStorage->getToken())
            $this->user = $tokenStorage->getToken()->getUser();
    }

    public function addUser(Post $post)
    {
        return $post->setUser($this->user);
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
        if ($this->isHostPost($post) && $post->isEditMode() || $this->haveUserRoleAdmin()) {

        }
        return false;
    }

    public function canEditPost(Post $post) {
        if ($this->isHostPost($post) && $post->isEditMode() || $this->haveUserRoleAdmin()) {
            return true;
        }
        return false;
    }

    public function canDeletePost(Post $post) {
        return $this->isHostPost($post);
    }

}