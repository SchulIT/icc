<?php

namespace App\Entity;

use App\Validator\Color;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
#[UniqueEntity(fields: ['externalId'])]
class AppointmentCategory {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Color()
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $color = null;

    /**
     * Determines whether non-admin users can add appointments in this category
     *
     * @ORM\Column(type="boolean")
     */
    private bool $usersCanCreateAppointments = false;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): AppointmentCategory {
        $this->externalId = $externalId;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): AppointmentCategory {
        $this->name = $name;
        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): AppointmentCategory {
        $this->color = $color;
        return $this;
    }

    public function isUsersCanCreateAppointments(): bool {
        return $this->usersCanCreateAppointments;
    }

    public function setUsersCanCreateAppointments(bool $usersCanCreateAppointments): AppointmentCategory {
        $this->usersCanCreateAppointments = $usersCanCreateAppointments;
        return $this;
    }
}