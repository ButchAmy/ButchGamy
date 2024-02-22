<?php

namespace App\Entity;

use App\Repository\FriendRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userA = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userB = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $requestedOn = null;

    #[ORM\Column]
    private ?bool $accepted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserA(): ?User
    {
        return $this->userA;
    }

    public function setUserA(?User $userA): static
    {
        $this->userA = $userA;

        return $this;
    }

    public function getUserB(): ?User
    {
        return $this->userB;
    }

    public function setUserB(?User $userB): static
    {
        $this->userB = $userB;

        return $this;
    }

    public function getRequestedOn(): ?\DateTimeInterface
    {
        return $this->requestedOn;
    }

    public function setRequestedOn(\DateTimeInterface $requestedOn): static
    {
        $this->requestedOn = $requestedOn;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }
}
