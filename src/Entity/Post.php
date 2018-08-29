<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
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

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->status = self::CONST_DRAFT;
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
        return self::CONST_DRAFT == $this->status || self::CONST_CHECKING == $this->status;
    }

    public function isVerified()
    {
        return self::CONST_VERIFIED == $this->status;
    }
}
