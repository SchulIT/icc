<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class DeviceToken {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=128)
     * @var string
     */
    private $token;

    /**
     * @ORM\Column(type="DeviceTokenType::class")
     * @var DeviceTokenType
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $registered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime|null
     */
    private $lastActive;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken(): ?string {
        return $this->token;
    }

    /**
     * @param string $token
     * @return DeviceToken
     */
    public function setToken(string $token): DeviceToken {
        $this->token = $token;
        return $this;
    }

    /**
     * @return DeviceTokenType
     */
    public function getType(): ?DeviceTokenType {
        return $this->type;
    }

    /**
     * @param DeviceTokenType $type
     * @return DeviceToken
     */
    public function setType(DeviceTokenType $type): DeviceToken {
        $this->type = $type;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * @param User $user
     * @return DeviceToken
     */
    public function setUser(User $user): DeviceToken {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return DeviceToken
     */
    public function setName(string $name): DeviceToken {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistered(): \DateTime {
        return $this->registered;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastActive(): ?\DateTime {
        return $this->lastActive;
    }

    /**
     * @param \DateTime $lastActive
     * @return DeviceToken
     */
    public function setLastActive(\DateTime $lastActive): DeviceToken {
        $this->lastActive = $lastActive;
        return $this;
    }
}