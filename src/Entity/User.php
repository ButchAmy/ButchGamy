<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
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
    private ?string $Username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ProfilePic = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ProfileBio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $FullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Country = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Birthday = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Gender = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $CreatedOn = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $UpdatedOn = null;

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
        return $this->Username;
    }

    public function setUsername(string $Username): static
    {
        $this->Username = $Username;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->ProfilePic;
    }

    public function setProfilePic(?string $ProfilePic): static
    {
        $this->ProfilePic = $ProfilePic;

        return $this;
    }

    public function getProfileBio(): ?string
    {
        return $this->ProfileBio;
    }

    public function setProfileBio(?string $ProfileBio): static
    {
        $this->ProfileBio = $ProfileBio;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->FullName;
    }

    public function setFullName(?string $FullName): static
    {
        $this->FullName = $FullName;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->Country;
    }

    public function setCountry(?string $Country): static
    {
        $this->Country = $Country;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->Birthday;
    }

    public function setBirthday(?\DateTimeInterface $Birthday): static
    {
        $this->Birthday = $Birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->Gender;
    }

    public function setGender(?string $Gender): static
    {
        $this->Gender = $Gender;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->CreatedOn;
    }

    public function setCreatedOn(\DateTimeInterface $CreatedOn): static
    {
        $this->CreatedOn = $CreatedOn;

        return $this;
    }

    public function getUpdatedOn(): ?\DateTimeInterface
    {
        return $this->UpdatedOn;
    }

    public function setUpdatedOn(\DateTimeInterface $UpdatedOn): static
    {
        $this->UpdatedOn = $UpdatedOn;

        return $this;
    }
}
