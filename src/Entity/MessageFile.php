<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class MessageFile {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Message::class, cascade: ['persist'], inversedBy: 'files')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Message $message = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $label = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $extension = null;

    /**
     * @var Collection<MessageFileUpload>
     */
    #[ORM\OneToMany(targetEntity: MessageFileUpload::class, mappedBy: 'messageFile')]
    private $uploads;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->uploads = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getMessage(): Message {
        return $this->message;
    }

    public function setMessage(Message $message): MessageFile {
        $this->message = $message;
        return $this;
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function setLabel(?string $label): MessageFile {
        $this->label = $label;
        return $this;
    }

    public function getExtension(): ?string {
        return $this->extension;
    }

    public function setExtension(?string $extension): MessageFile {
        $this->extension = $extension;
        return $this;
    }

    public function getUploads(): Collection {
        return $this->uploads;
    }
}