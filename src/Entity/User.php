<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePic = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $profileBio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdOn = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedOn = null;

    #[ORM\Column]
    private ?bool $admin = null;

    #[ORM\Column]
    private ?bool $loginAllowed = null;

    #[ORM\Column]
    private ?bool $friendAllowed = null;

    #[ORM\Column]
    private ?bool $messageAllowed = null;

    #[ORM\OneToMany(mappedBy: 'developer', targetEntity: Game::class, orphanRemoval: true)]
    private Collection $gamesDeveloped;

    public function __construct()
    {
        $this->gamesDeveloped = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePic(?string $profilePic): static
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    public function getProfileBio(): ?string
    {
        return $this->profileBio;
    }

    public function setProfileBio(?string $profileBio): static
    {
        $this->profileBio = $profileBio;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): static
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getUpdatedOn(): ?\DateTimeInterface
    {
        return $this->updatedOn;
    }

    public function setUpdatedOn(\DateTimeInterface $updatedOn): static
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    public function isLoginAllowed(): ?bool
    {
        return $this->loginAllowed;
    }

    public function setLoginAllowed(bool $loginAllowed): static
    {
        $this->loginAllowed = $loginAllowed;

        return $this;
    }

    public function isFriendAllowed(): ?bool
    {
        return $this->friendAllowed;
    }

    public function setFriendAllowed(bool $friendAllowed): static
    {
        $this->friendAllowed = $friendAllowed;

        return $this;
    }

    public function isMessageAllowed(): ?bool
    {
        return $this->messageAllowed;
    }

    public function setMessageAllowed(bool $messageAllowed): static
    {
        $this->messageAllowed = $messageAllowed;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesDeveloped(): Collection
    {
        return $this->gamesDeveloped;
    }

    public function addGamesDeveloped(Game $gamesDeveloped): static
    {
        if (!$this->gamesDeveloped->contains($gamesDeveloped)) {
            $this->gamesDeveloped->add($gamesDeveloped);
            $gamesDeveloped->setDeveloper($this);
        }

        return $this;
    }

    public function removeGamesDeveloped(Game $gamesDeveloped): static
    {
        if ($this->gamesDeveloped->removeElement($gamesDeveloped)) {
            // set the owning side to null (unless already changed)
            if ($gamesDeveloped->getDeveloper() === $this) {
                $gamesDeveloped->setDeveloper(null);
            }
        }

        return $this;
    }
}
