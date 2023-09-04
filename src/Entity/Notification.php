<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Notification {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $recipient;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $subject;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $link;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    private ?string $linkText;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $isRead = false;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return User
     */
    public function getRecipient(): User {
        return $this->recipient;
    }

    /**
     * @param User $recipient
     * @return Notification
     */
    public function setRecipient(User $recipient): Notification {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return Notification
     */
    public function setSubject(?string $subject): Notification {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Notification
     */
    public function setContent(?string $content): Notification {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return Notification
     */
    public function setLink(?string $link): Notification {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkText(): ?string {
        return $this->linkText;
    }

    /**
     * @param string|null $linkText
     * @return Notification
     */
    public function setLinkText(?string $linkText): Notification {
        $this->linkText = $linkText;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Notification
     */
    public function setCreatedAt(DateTime $createdAt): Notification {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRead(): bool {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     * @return Notification
     */
    public function setIsRead(bool $isRead): Notification {
        $this->isRead = $isRead;
        return $this;
    }
}