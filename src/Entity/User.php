<?php
// src/Entity/User.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface
{
    const CONST_ACTIVE = "Active";
    const CONST_BLOCKED = "Blocked";
    const CONST_NOT_VERIFIED = "Not verified";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message = "Name must be filled")
     * @Assert\Length(
     *      min = 2,
     *      max = 150,
     *      minMessage = "Field 'Name' is not filled must contain more than {{ limit }} characters",
     *      maxMessage = "The 'Name' must not exceed {{ limit }} characters in length"
     * )
     */
    private $name;

    /**
     * @Assert\Length(max=4096)
     * @Assert\NotBlank(message = "Password must be filled")
     * @Assert\Length(
     *      min = 8,
     *      max = 4096,
     *      minMessage = "Field 'Password' is not filled must contain more than {{ limit }} characters",
     *      maxMessage = "The 'Password' must not exceed {{ limit }} characters in length"
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $verificationToken;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * One User has Many Posts.
     * @ORM\OneToMany(targetEntity="Post", mappedBy="user")
     */
    private $posts;

    /**
     * One User has Many Comment.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     */
    private $comments;

    /**
     * One User has Many Assessments.
     * @ORM\OneToMany(targetEntity="Assessment", mappedBy="user")
     */
    private $assessments;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    private $status;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->status = self::CONST_NOT_VERIFIED;
        $this->verificationToken = hash('md5', uniqid());
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->assessments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function activateUser()
    {
        $this->status = self::CONST_ACTIVE;

        $this->verificationToken = '';

        return $this;
    }

    public function isActive() {
        return strlen($this->verificationToken) ? false : true;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $assessment->setUser($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): self
    {
        if ($this->assessments->contains($assessment)) {
            $this->assessments->removeElement($assessment);
            // set the owning side to null (unless already changed)
            if ($assessment->getUser() === $this) {
                $assessment->setUser(null);
            }
        }

        return $this;
    }

    public function getRating()
    {
        $rating = 0;

        foreach ($this->posts as $post) {
            $ratingPost = $post->getRatingPost();
            $rating = $rating + $ratingPost;
        }

        return $rating;
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

    public function getArrayUsers()
    {
        return [
            $this->id,
            $this->email
        ];
    }

    public static function getUserStatuses()
    {
        return [
            self::CONST_ACTIVE => self::CONST_ACTIVE,
            self::CONST_BLOCKED => self::CONST_BLOCKED
        ];
    }

    public function setBlockStatusUser(): self
    {
        $this->status = self::CONST_BLOCKED;
        return $this;
    }

    public function setActiveStatusUser(): self
    {
        $this->status = self::CONST_ACTIVE;
        return $this;
    }

    public function isNotVerifiedUser()
    {
        return  $this->status == self::CONST_NOT_VERIFIED;
    }

    public function isBlockedUser()
    {
        return  $this->status == self::CONST_BLOCKED;
    }

    public function isActivateStatusUser()
    {
        return $this->status == self::CONST_ACTIVE;
    }

    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->roles) ? true : false;
    }

    public function isBlocked()
    {
        return $this->status == self::CONST_BLOCKED;
    }
}