<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class MessagePollChoice {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="pollChoices")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Message $message = null;

    /**
     * @ORM\Column(type="string", name="label")
     */
    #[Assert\NotBlank]
    private ?string $label = null;

    /**
     * @ORM\Column(type="text", name="description", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $description = null;

    /**
     * @ORM\Column(type="integer", name="mininum")
     */
    #[Assert\GreaterThanOrEqual(0)]
    private int $minimum = 0;

    /**
     * @ORM\Column(type="integer", name="maximum")
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'minimum')]
    private int $maximum = 0;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getMessage(): Message {
        return $this->message;
    }

    public function setMessage(Message $message): MessagePollChoice {
        $this->message = $message;
        return $this;
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function setLabel(?string $label): MessagePollChoice {
        $this->label = $label;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): MessagePollChoice {
        $this->description = $description;
        return $this;
    }

    public function getMinimum(): int {
        return $this->minimum;
    }

    public function setMinimum(int $minimum): MessagePollChoice {
        $this->minimum = $minimum;
        return $this;
    }

    public function getMaximum(): int {
        return $this->maximum;
    }

    public function setMaximum(int $maximum): MessagePollChoice {
        $this->maximum = $maximum;
        return $this;
    }
}