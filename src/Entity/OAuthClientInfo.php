<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;

/**
 * Helps to store additional information about an OAuth client as
 * the oauth2-bundle does not support client names (yet?!).
 *
 * As soon as https://github.com/trikoder/oauth2-bundle/issues/145 is implemented,
 * we should use this instead of an additional class.
 *
 * @ORM\Entity()
 */
class OAuthClientInfo {
    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="Trikoder\Bundle\OAuth2Bundle\Model\Client")
     * @ORM\JoinColumn(onDelete="CASCADE", referencedColumnName="identifier")
     * @Assert\NotNull()
     * @var Client|null
     */
    private $client;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return OAuthClientInfo
     */
    public function setName(?string $name): OAuthClientInfo {
        $this->name = $name;
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
     * @return OAuthClientInfo
     */
    public function setDescription(?string $description): OAuthClientInfo {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Client|null
     */
    public function getClient(): ?Client {
        return $this->client;
    }

    /**
     * @param Client|null $client
     * @return OAuthClientInfo
     */
    public function setClient(?Client $client): OAuthClientInfo {
        $this->client = $client;
        return $this;
    }
}