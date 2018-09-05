<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Services\CommentService;
use App\Services\PostService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    const EDIT = 'EDIT';

    private $decisionManager;
    private $commentService;

    public function __construct(AccessDecisionManagerInterface $decisionManager, CommentService $commentService)
    {
        $this->decisionManager = $decisionManager;
        $this->commentService = $commentService;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canEdit(Comment $comment)
    {
        return $this->commentService->canEditComment($comment);
    }
}
