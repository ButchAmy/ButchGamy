<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

	#[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'conversations')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

	/**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

	public function getUpdatedOn(): ?\DateTimeInterface
	{
		$mostRecentMessageTime = null;
		foreach ($this->getMessages() as $message) {
			if ($message->getCreatedOn() > $mostRecentMessageTime) {
				$mostRecentMessageTime = $message->getCreatedOn();
			}
		}
		return $mostRecentMessageTime;
	}

	public function getDisplayMessageFor(User $appUser): ?Message
	{
		// Returns preview message to display in inbox. Returns the earliest unread message if there are any, otherwise returns the most recent message.
		foreach ($this->getMessages() as $message) { // Originally used isUnread as an initial if conditional, but that just meant looping over the collection twice
			if ($message->isUnread() == true) {
				if ($message->getUserTo() == $appUser) {
					return $message;
				}
			}
		}
		return $this->getMessages()->last();
	}
}
