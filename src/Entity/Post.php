<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    const CONST_DRAFT = 'Draft';
    const CONST_ON_MODERATION = 'On moderation';
    const CONST_DECLINED= 'Declined';
    const CONST_PUBLISHED = 'Published';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=400)
     * @Assert\NotBlank(message = "Title must be filled")
     * @Assert\Length(
     *      min = 2,
     *      max = 400,
     *      minMessage = "Field 'Title' is not filled must contain more than {{ limit }} characters",
     *      maxMessage = "The 'Title' must not exceed {{ limit }} characters in length"
     * )
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
     * @Assert\NotBlank(message = "Description must be filled")
     * @Assert\Length(
     *      min = 20,
     *      max = 2000,
     *      minMessage = "Field 'Description' is not filled must contain more than {{ limit }} characters",
     *      maxMessage = "The 'Description' must not exceed {{ limit }} characters in length"
     * )
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
            self::CONST_ON_MODERATION => Post::CONST_ON_MODERATION,
            self::CONST_PUBLISHED => Post::CONST_PUBLISHED,
            self::CONST_DECLINED => Post::CONST_DECLINED
        ];
    }

    public function isEditMode()
    {
        return self::CONST_DRAFT == $this->status;
    }

    public function isVerified()
    {
        return self::CONST_ON_MODERATION == $this->status || self::CONST_PUBLISHED == $this->status;
    }

    public function declined(): self
    {
        $this->status = self::CONST_DECLINED;

        return $this;
    }

    public function moderated(): self
    {
        $this->status = self::CONST_ON_MODERATION;

        return $this;
    }

    public function published(): self
    {
        $this->publicationDate = new \DateTime();

        $this->status = self::CONST_PUBLISHED;

        return $this;
    }

    public function isDraftStatus()
    {
        return $this->status == self::CONST_DRAFT ? true : false;
    }

    public function isOnModerationStatus()
    {
        return $this->status == self::CONST_ON_MODERATION ? true : false;
    }

    public function isPublishedStatus()
    {
        return $this->status == self::CONST_DECLINED ? true : false;
    }

    public function isOnDeclinedStatus()
    {
        return $this->status == self::CONST_PUBLISHED ? true : false;
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

    public function getRatingPost()
    {
        $rating = 0;

        foreach ($this->assessments as $assessment) {
            if ($assessment->getAssessment() === true) {
                $rating++;
            } else {
                $rating--;
            }
        }
        return $rating;
    }

    public function getIsUserEvaluated(User $user) {

        foreach ($this->assessments as $assessment) {
            if($assessment->getUser() == $user)
                return true;
        }

        return false;
    }

    public function getIsUserAsseeement(User $user) {

        foreach ($this->assessments as $assessment) {
            if($assessment->getUser() == $user)
                return $assessment->getAssessment();
        }
    }
}
