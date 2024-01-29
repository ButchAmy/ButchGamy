<?php

namespace App\Entity;

use App\Repository\AchievementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchievementRepository::class)]
class Achievement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'achievements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'achievement', targetEntity: UserAchievement::class, orphanRemoval: true)]
    private Collection $achievers;

    public function __construct()
    {
        $this->achievers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, UserAchievement>
     */
    public function getAchievers(): Collection
    {
        return $this->achievers;
    }

    public function addAchiever(UserAchievement $achiever): static
    {
        if (!$this->achievers->contains($achiever)) {
            $this->achievers->add($achiever);
            $achiever->setAchievement($this);
        }

        return $this;
    }

    public function removeAchiever(UserAchievement $achiever): static
    {
        if ($this->achievers->removeElement($achiever)) {
            // set the owning side to null (unless already changed)
            if ($achiever->getAchievement() === $this) {
                $achiever->setAchievement(null);
            }
        }

        return $this;
    }
}
