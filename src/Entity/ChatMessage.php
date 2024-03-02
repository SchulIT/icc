<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ChatMessage {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Chat::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Chat $chat;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private User $createdBy;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content;

    /**
     * @var Collection<ChatMessageAttachment>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: ChatMessageAttachment::class, cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $attachments;


    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->attachments = new ArrayCollection();
    }

    public function getChat(): Chat {
        return $this->chat;
    }

    public function setChat(Chat $chat): ChatMessage {
        $this->chat = $chat;
        return $this;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): ChatMessage {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): ChatMessage {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): ChatMessage {
        $this->content = $content;
        return $this;
    }

    public function addAttachment(ChatMessageAttachment $attachment): void {
        $attachment->setMessage($this);
        $this->attachments->add($attachment);
    }

    public function removeAttachment(ChatMessageAttachment $attachment): void {
        $this->attachments->removeElement($attachment);
    }

    public function getAttachments(): Collection {
        return $this->attachments;
    }
}