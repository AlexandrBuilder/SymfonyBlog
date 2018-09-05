<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 31.08.18
 * Time: 10:56
 */

namespace App\Services;

use App\Entity\Assessment;
use App\Entity\Post;
use App\Repository\AssessmentRepository;
use Doctrine\ORM\EntityManager;
use LogicException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class AssessmentService
{
    private $user;
    private $assessmentRepository;
    private $entityManager;
    private $security;

    public function __construct(
        AssessmentRepository $assessmentRepository,
        TokenStorageInterface $tokenStorage,
        EntityManager $entityManager,
        Security $security
    ) {
        $this->assessmentRepository = $assessmentRepository;
        if ($tokenStorage->getToken()) {
            $this->user = $tokenStorage->getToken()->getUser();
        }
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function canEditAssessment(Post $post)
    {
        if (isset($this->user) && !($this->user->isBlocked())) {
            return $this->user == $post->getUser();
        }
        return false;
    }

    public function createForUser(string $assessmentValue, Post $post)
    {

        if ($this->canEditAssessment($post)) {
            throw new LogicException('You can not put yourself assessments');
        }

        if ($this->user->isBlocked()) {
            throw new AccessDeniedException('This user is blocked');
        }

        $assessment = new Assessment();
        $assessment->setUser($this->user)
            ->setPost($post);

        if ($assessmentValue == Assessment::POSSITIVE_ASSESMENT) {
            $assessment->setPossitiveAssessment();
        } elseif ($assessmentValue == Assessment::NEGATIVE_ASSESMENT) {
            $assessment->setNegativeAssessment();
        } else {
            throw new BadRequestHttpException('Invalid query assessment');
        }

        $oldAssessment = $this->assessmentRepository->findByUserAndPost($post, $this->user);

        if (isset($oldAssessment)) {
            if ($assessment->equalAssessment($oldAssessment)) {
                throw new LogicException('Assessment already exist');
            } else {
                $this->deleteForUser($post);
            }
        }

        $this->entityManager->persist($assessment);
        $this->entityManager->flush();
        return $assessment;
    }

    public function deleteForUser(Post $post)
    {
        if ($this->user->isBlocked()) {
            throw new AccessDeniedException('This user is blocked');
        }

        if (!$this->assessmentRepository->findByUserAndPost($post, $this->user)) {
            throw new LogicException('Assessment not exist');
        }
        $assessment = $this->assessmentRepository->findByUserAndPost($post, $this->user);
        $this->entityManager->remove($assessment);
        $this->entityManager->flush();
        return $assessment;
    }

    public function delete(Assessment $assessment)
    {
        $this->entityManager->remove($assessment);
        $this->entityManager->flush();
    }
}
