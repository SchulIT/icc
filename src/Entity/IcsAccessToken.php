<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class IcsAccessToken implements UserInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, length=128)
     * @var string
     */
    private $token;

    /**
     * @ORM\Column(type="ics_access_token_type")
     * @var IcsAccessTokenType
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
     * @var DateTime
     */
    private $registered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $lastActive;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getToken(): ?string {
        return $this->token;
    }

    /**
     * @param string $token
     * @return IcsAccessToken
     */
    public function setToken(string $token): IcsAccessToken {
        $this->token = $token;
        return $this;
    }

    /**
     * @return IcsAccessTokenType
     */
    public function getType(): ?IcsAccessTokenType {
        return $this->type;
    }

    /**
     * @param IcsAccessTokenType $type
     * @return IcsAccessToken
     */
    public function setType(IcsAccessTokenType $type): IcsAccessToken {
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
     * @return IcsAccessToken
     */
    public function setUser(User $user): IcsAccessToken {
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
     * @return IcsAccessToken
     */
    public function setName(string $name): IcsAccessToken {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistered(): DateTime {
        return $this->registered;
    }

    /**
     * @return DateTime|null
     */
    public function getLastActive(): ?DateTime {
        return $this->lastActive;
    }

    /**
     * @param DateTime $lastActive
     * @return IcsAccessToken
     */
    public function setLastActive(DateTime $lastActive): IcsAccessToken {
        $this->lastActive = $lastActive;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return $this->user->getRoles();
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->user->getPassword();
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return $this->user->getSalt();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        $this->user->eraseCredentials();
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->user->getUsername();
    }

    public function getUserIdentifier(): string {
        return $this->user->getUserIdentifier();
    }
}