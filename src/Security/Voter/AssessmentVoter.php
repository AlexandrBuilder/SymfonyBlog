<?php

namespace App\Security\Voter;

use App\Entity\Assessment;
use App\Entity\User;
use App\Services\AssessmentService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AssessmentVoter extends Voter
{
    const EDIT = 'EDIT';

    private $decisionManager;
    private $assessmentService;

    public function __construct(AccessDecisionManagerInterface $decisionManager, AssessmentService $assessmentService)
    {
        $this->decisionManager = $decisionManager;
        $this->assessmentService = $assessmentService;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof Assessment;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($attribute);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canEdit(Assessment $assessment)
    {
        return $this->assessmentService->canEditAssessment($assessment);
    }
}
