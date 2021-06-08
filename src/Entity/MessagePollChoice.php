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
     * @var Message
     */
    private $message;

    /**
     * @ORM\Column(type="string", name="label")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $label;

    /**
     * @ORM\Column(type="text", name="description", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\Column(type="integer", name="mininum")
     * @Assert\GreaterThanOrEqual(0)
     * @var int
     */
    private $minimum = 0;

    /**
     * @ORM\Column(type="integer", name="maximum")
     * @Assert\GreaterThanOrEqual(propertyPath="minimum")
     * @var int
     */
    private $maximum = 0;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Message
     */
    public function getMessage(): Message {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessagePollChoice
     */
    public function setMessage(Message $message): MessagePollChoice {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return MessagePollChoice
     */
    public function setLabel(?string $label): MessagePollChoice {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return MessagePollChoice
     */
    public function setDescription(?string $description): MessagePollChoice {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinimum(): int {
        return $this->minimum;
    }

    /**
     * @param int $minimum
     * @return MessagePollChoice
     */
    public function setMinimum(int $minimum): MessagePollChoice {
        $this->minimum = $minimum;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaximum(): int {
        return $this->maximum;
    }

    /**
     * @param int $maximum
     * @return MessagePollChoice
     */
    public function setMaximum(int $maximum): MessagePollChoice {
        $this->maximum = $maximum;
        return $this;
    }
}