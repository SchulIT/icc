<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class MessageFile {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Message $message = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $label = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $extension = null;

    /**
     * @ORM\OneToMany(targetEntity="MessageFileUpload", mappedBy="messageFile")
     * @var Collection<MessageFileUpload>
     */
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