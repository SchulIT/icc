<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class MessagePollVote {
    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User|null
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="pollVotes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Message|null
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity="MessagePollVoteRankedChoice", cascade={"persist"}, orphanRemoval=true, mappedBy="vote")
     * @var Collection<MessagePollVoteRankedChoice>
     */
    private $choices;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="MessagePollChoice")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var MessagePollChoice|null
     */
    private $assignedChoice;

    public function __construct() {
        $this->choices = new ArrayCollection();
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     * @return MessagePollVote
     */
    public function setUser(User $user): MessagePollVote {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessagePollVote
     */
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

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    /**
     * @return MessagePollChoice|null
     */
    public function getAssignedChoice(): ?MessagePollChoice {
        return $this->assignedChoice;
    }

    /**
     * @param MessagePollChoice|null $assignedChoice
     * @return MessagePollVote
     */
    public function setAssignedChoice(?MessagePollChoice $assignedChoice): MessagePollVote {
        $this->assignedChoice = $assignedChoice;
        return $this;
    }

    public function isCompleted(): bool {
        return count($this->getMessage()->getPollChoices()) >= $this->getChoices()->count();
    }
}