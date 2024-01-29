<?php

namespace App\Entity;

use App\Repository\UserAchievementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAchievementRepository::class)]
class UserAchievement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'achievements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'achievers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Achievement $achievement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $achievedOn = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAchievement(): ?Achievement
    {
        return $this->achievement;
    }

    public function setAchievement(?Achievement $achievement): static
    {
        $this->achievement = $achievement;

        return $this;
    }

    public function getAchievedOn(): ?\DateTimeInterface
    {
        return $this->achievedOn;
    }

    public function setAchievedOn(\DateTimeInterface $achievedOn): static
    {
        $this->achievedOn = $achievedOn;

        return $this;
    }
}
