<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class StudentAbsenceType {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private ?string $name;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $mustApprove = false;

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
     * @return StudentAbsenceType
     */
    public function setName(?string $name): StudentAbsenceType {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMustApprove(): bool {
        return $this->mustApprove;
    }

    /**
     * @param bool $mustApprove
     * @return StudentAbsenceType
     */
    public function setMustApprove(bool $mustApprove): StudentAbsenceType {
        $this->mustApprove = $mustApprove;
        return $this;
    }

    public function __toString(): string {
        return $this->getName();
    }
}