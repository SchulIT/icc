<?php

namespace App\Entity;

use App\Validator\Color;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 */
class AppointmentCategory {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Color()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string|null
     */
    private $color = null;

    /**
     * Determines whether non-admin users can add appointments in this category
     *
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $usersCanCreateAppointments = false;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return AppointmentCategory
     */
    public function setExternalId(?string $externalId): AppointmentCategory {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return AppointmentCategory
     */
    public function setName(?string $name): AppointmentCategory {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string {
        return $this->color;
    }

    /**
     * @param string|null $color
     * @return AppointmentCategory
     */
    public function setColor(?string $color): AppointmentCategory {
        $this->color = $color;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUsersCanCreateAppointments(): bool {
        return $this->usersCanCreateAppointments;
    }

    /**
     * @param bool $usersCanCreateAppointments
     * @return AppointmentCategory
     */
    public function setUsersCanCreateAppointments(bool $usersCanCreateAppointments): AppointmentCategory {
        $this->usersCanCreateAppointments = $usersCanCreateAppointments;
        return $this;
    }
}