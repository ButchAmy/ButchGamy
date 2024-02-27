<?php

namespace App\Entity;

use App\Repository\GameRepository;
use App\Repository\GameResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 511)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'gamesDeveloped')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $developer = null;

    #[ORM\Column(length: 255)]
    private ?string $apiKey = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdOn = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedOn = null;

    #[ORM\Column]
    private ?bool $public = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Achievement::class, orphanRemoval: true)]
    private Collection $achievements;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameResult::class, orphanRemoval: true)]
    private Collection $gameResults;

    public function __construct()
    {
        $this->achievements = new ArrayCollection();
        $this->gameResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDeveloper(): ?User
    {
        return $this->developer;
    }

    public function setDeveloper(?User $developer): static
    {
        $this->developer = $developer;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

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

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return Collection<int, Achievement>
     */
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

	public function getUniqueAchievements(): Array
	{
		$uniqueAchievements = [];
		foreach ($this->achievements as $userAchievement) {
			$unique = true;
			foreach ($uniqueAchievements as $uniqueAchievement) {
				if ($userAchievement->getName() == $uniqueAchievement->getName()) {
					$unique = false;
				}
			}
			if ($unique) {
				$uniqueAchievements[] = $userAchievement;
			}
		}
		return $uniqueAchievements;
	}

    public function addAchievement(Achievement $achievement): static
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements->add($achievement);
            $achievement->setGame($this);
        }

        return $this;
    }

    public function removeAchievement(Achievement $achievement): static
    {
        if ($this->achievements->removeElement($achievement)) {
            // set the owning side to null (unless already changed)
            if ($achievement->getGame() === $this) {
                $achievement->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameResult>
     */
    public function getGameResults(): Collection
    {
        return $this->gameResults;
    }

    public function addGameResult(GameResult $gameResult): static
    {
        if (!$this->gameResults->contains($gameResult)) {
            $this->gameResults->add($gameResult);
            $gameResult->setGame($this);
        }

        return $this;
    }

    public function removeGameResult(GameResult $gameResult): static
    {
        if ($this->gameResults->removeElement($gameResult)) {
            // set the owning side to null (unless already changed)
            if ($gameResult->getGame() === $this) {
                $gameResult->setGame(null);
            }
        }

        return $this;
    }

	public function getPlayerCount(): int
	{
		$playerArray = [];
		foreach ($this->getGameResults() as $gameResult) {
			if (!in_array($gameResult->getUser(), $playerArray)) {
				$playerArray[] = $gameResult->getUser();
			}
		}
		return count($playerArray);
	}

	public function getHash(User $user): string
	{
		return md5($this->getApiKey() . $user->getId() . 'I_LOVE_SMURFS');
	}

	public function isViewAllowedBy(User $user): bool
	{
		// Viewing of public games is allowed by anyone
		if ($this->isPublic()) {
			return true;
		}
		// Viewing of private games is allowed by the developer, by admins...
		if ($user === $this->getDeveloper() || $user->isAdmin()) {
			return true;
		}
		// ...and by friends of the developer
		if (in_array($user, $this->getDeveloper()->getFriends()->toArray())) {
			return true;
		}
		return false;
	}

	public function isEditAllowedBy(User $user): bool
	{
		// Editing is ONLY allowed by the developer, and by admins
		if ($user === $this->getDeveloper() || $user->isAdmin()) {
			return true;
		}
		return false;
	}
}
