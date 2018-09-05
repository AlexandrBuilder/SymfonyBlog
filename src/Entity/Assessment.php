<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentRepository")
 */
class Assessment
{
    const POSSITIVE_ASSESMENT = "like";
    const NEGATIVE_ASSESMENT = "dislike";
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $assessment;

    /**
     * Many Assessment have One Post.
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="assessments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;

    /**
     * Many Assessment have One User.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assessments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssessment(): ?bool
    {
        return $this->assessment;
    }

    public function setPossitiveAssessment(): self
    {
        $this->assessment = true;

        return $this;
    }

    public function setNegativeAssessment(): self
    {
        $this->assessment = false;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function equalAssessment(Assessment $assessment)
    {
        if ($assessment->getUser() == $this->user && $assessment->getAssessment() == $this->assessment
            && $assessment->getPost() == $this->post) {
            return true;
        }
        return false;
    }
}
