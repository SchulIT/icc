<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MessagePollVote {
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'pollVotes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Message $message = null;

    /**
     * @var Collection<MessagePollVoteRankedChoice>
     */
    #[ORM\OneToMany(mappedBy: 'vote', targetEntity: MessagePollVoteRankedChoice::class, cascade: ['persist'], orphanRemoval: true)]
    private $choices;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: MessagePollChoice::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?MessagePollChoice $assignedChoice = null;

    public function __construct() {
        $this->choices = new ArrayCollection();
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): MessagePollVote {
        $this->user = $user;
        return $this;
    }

    public function getMessage(): Message {
        return $this->message;
    }

    public function setMessage(Message $message): MessagePollVote {
        $this->message = $message;
        return $this;
    }

    public function addChoice(MessagePollVoteRankedChoice $choice): void {
        $this->choices->add($choice);
    }

    public function getChoices(): Collection {
        return $this->choices;
    }

    public function getChoice(int $rank): ?MessagePollVoteRankedChoice {
        /** @var MessagePollVoteRankedChoice $choice */
        foreach($this->getChoices() as $choice) {
            if($choice->getRank() === $rank) {
                return $choice;
            }
        }

        return null;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getAssignedChoice(): ?MessagePollChoice {
        return $this->assignedChoice;
    }

    public function setAssignedChoice(?MessagePollChoice $assignedChoice): MessagePollVote {
        $this->assignedChoice = $assignedChoice;
        return $this;
    }

    public function isCompleted(): bool {
        return count($this->getMessage()->getPollChoices()) >= $this->getChoices()->count();
    }
}