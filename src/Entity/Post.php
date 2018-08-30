<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    const CONST_DRAFT = 'Draft';
    const CONST_CHECKING = 'Checking';
    const CONST_VERIFIED = 'Verified';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=400)
     */
    private $title;

    /**
     * Many Post have Many Tags.
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts")
     * @ORM\JoinTable(name="tags_posts")
     */
    private $tags;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $status;

    /**
     * Many Posts have One User.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * One Post has Many Comment.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    private $comments;

    /**
     * One Post has Many Assessments.
     * @ORM\OneToMany(targetEntity="Assessment", mappedBy="post")
     */
    private $assessments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publicationDate;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->status = self::CONST_DRAFT;
        $this->publicationDate = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->assessments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function __toString () {
        return $this->title;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public static function getPostStatus()
    {
        return [
            self::CONST_DRAFT => Post::CONST_DRAFT,
            self::CONST_CHECKING => Post::CONST_CHECKING,
            self::CONST_VERIFIED => Post::CONST_VERIFIED
        ];
    }

    public function isEditMode()
    {
        return self::CONST_DRAFT == $this->status;
    }

    public function isVerified()
    {
        return self::CONST_VERIFIED == $this->status || self::CONST_CHECKING == $this->status;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Assessment[]
     */
    public function getAssessments(): Collection
    {
        return $this->assessments;
    }

    public function addAssessment(Assessment $assessment): self
    {
        if (!$this->assessments->contains($assessment)) {
            $this->assessments[] = $assessment;
            $assessment->setPost($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): self
    {
        if ($this->assessments->contains($assessment)) {
            $this->assessments->removeElement($assessment);
            // set the owning side to null (unless already changed)
            if ($assessment->getPost() === $this) {
                $assessment->setPost(null);
            }
        }

        return $this;
    }
}
