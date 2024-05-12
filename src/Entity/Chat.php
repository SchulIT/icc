<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Chat {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $topic;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private User|null $createdBy;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[Assert\Count(min: 1)]
    private Collection $participants;

    /**
     * @var Collection<ChatMessage>
     */
    #[ORM\OneToMany(mappedBy: 'chat', targetEntity: ChatMessage::class, cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $messages;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getTopic(): ?string {
        return $this->topic;
    }

    public function setTopic(?string $topic): Chat {
        $this->topic = $topic;
        return $this;
    }

    public function addParticipants(User $user): void {
        $this->participants->add($user);
    }

    public function removeParticipants(User $user): void {
        $this->participants->removeElement($user);
    }

    /**
     * @return Collection<User>
     */
    public function getParticipants(): Collection {
        return $this->participants;
    }

    public function addMessage(ChatMessage $message): void {
        $message->setChat($this);
        $this->messages->add($message);
    }

    /**
     * @return Collection<ChatMessage>
     */
    public function getMessages(): Collection {
        return $this->messages;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): Chat {
        $this->createdBy = $createdBy;
        return $this;
    }
}