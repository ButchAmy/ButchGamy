<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use App\Repository\FriendRequestRepository;
use App\Repository\GameResultsRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
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

    #[ORM\Column(length: 511, nullable: true)]
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

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: GameResults::class, orphanRemoval: true)]
    private Collection $gameResults;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'friends')]
    private Collection $friends;

	#[ORM\OneToMany(mappedBy: 'userTo', targetEntity: FriendRequest::class, orphanRemoval: true)]
	private Collection $friendRequestsReceived;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Achievement::class, orphanRemoval: true)]
    private Collection $achievements;

    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'users')]
    private Collection $conversations;

    public function __construct()
    {
        $this->gamesDeveloped = new ArrayCollection();
        $this->gameResults = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->achievements = new ArrayCollection();
        $this->conversations = new ArrayCollection();
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

    /**
     * @return Collection<int, GameResults>
     */
    public function getGameResults(): Collection
    {
        return $this->gameResults;
    }

    public function addGameResult(GameResults $gameResult): static
    {
        if (!$this->gameResults->contains($gameResult)) {
            $this->gameResults->add($gameResult);
            $gameResult->setUser($this);
        }

        return $this;
    }

    public function removeGameResult(GameResults $gameResult): static
    {
        if ($this->gameResults->removeElement($gameResult)) {
            // set the owning side to null (unless already changed)
            if ($gameResult->getUser() === $this) {
                $gameResult->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): static
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
        }

        return $this;
    }

    public function removeFriend(self $friend): static
    {
        $this->friends->removeElement($friend);

        return $this;
    }

	/**
     * @return Collection<int, FriendRequest>
     */
    public function getFriendRequestsReceived(): Collection
    {
        return $this->friendRequestsReceived;
    }

    public function addFriendRequestReceived(FriendRequest $friendRequestReceived): static
    {
        if (!$this->friendRequestsReceived->contains($friendRequestReceived)) {
            $this->friendRequestsReceived->add($friendRequestReceived);
            $friendRequestReceived->setUserTo($this);
        }

        return $this;
    }

    public function removeFriendRequestReceived(FriendRequest $friendRequestReceived): static
    {
        if ($this->friendRequestsReceived->removeElement($friendRequestReceived)) {
            // set the owning side to null (unless already changed)
            if ($friendRequestReceived->getUserTo() === $this) {
                $friendRequestReceived->setUserTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Achievement>
     */
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(Achievement $achievement): static
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements->add($achievement);
            $achievement->setUser($this);
        }

        return $this;
    }

    public function removeAchievement(Achievement $achievement): static
    {
        if ($this->achievements->removeElement($achievement)) {
            // set the owning side to null (unless already changed)
            if ($achievement->getUser() === $this) {
                $achievement->setUser(null);
            }
        }

        return $this;
    }

	public function friendStatus(User $appUser, FriendRequestRepository $friendRequestRepository): int
				{
               		// Checks if this user and target user are friends and what the state is of a friend request between them, if it exists
               		// This is intended to be checked on the user being viewed; in other words, "appUser" refers to "$this->getUser()" aka the viewing user
               		// 0 = Not friends, 1 = Sent a friend request, 2 = Received a friend request, 3 = Friends
					if (in_array($appUser, $this->getFriends()->toArray())) {
						return 3;
					}
					if ($friendRequestRepository->findBy([ 'userFrom' => $this, 'userTo' => $appUser ])) {
						return 2;
					}
					if ($friendRequestRepository->findBy([ 'userFrom' => $appUser, 'userTo' => $this ])) {
						return 1;
					}
					return 0;
				}

	public function getPlayedCount(): int
				{
					$gameArray = [];
					foreach ($this->getGameResults() as $gameResult) {
						if (!in_array($gameResult->getGame(), $gameArray)) {
							$gameArray[] = $gameResult->getGame();
						}
					}
					return count($gameArray);
				}

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
        }

        return $this;
    }

	public function getNotificationCount(): int
	{
		$count = 0;
		foreach ($this->getFriendRequestsReceived() as $friendRequest) {
			if ($friendRequest->isAccepted() == false) {
				$count++;
			}
		}
		foreach ($this->getConversations() as $conversation) {
			if ($conversation->getDisplayMessageFor($this)->isUnread()) {
				$count++;
			}
		}
		return $count;
	}
}