<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Services\PostService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    const EDIT = 'EDIT';
    const VIEW = 'VIEW';
    const DELETE = 'VIEW';

    private $decisionManager;
    private $postService;

    public function __construct(AccessDecisionManagerInterface $decisionManager, PostService $postService)
    {
        $this->decisionManager = $decisionManager;
        $this->postService = $postService;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject);
                break;
            case self::VIEW:
                return $this->canView($subject);
                break;
            case self::DELETE:
                return $this->canDelete($subject);
                break;
        }

        return false;
    }

    public function canEdit(Post $post)
    {
        return $this->postService->canEditPost($post);
    }

    public function canView(Post $post)
    {
        return  $this->postService->canViewPost($post);
    }

    public function canDelete(Post $post)
    {
        return  $this->postService->canDeletePost($post);
    }
}
