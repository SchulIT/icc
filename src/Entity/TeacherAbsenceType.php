<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TeacherAbsenceType {
    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $details = null;

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
     * @return TeacherAbsenceType
     */
    public function setName(?string $name): TeacherAbsenceType {
        $this->name = $name;
        return $this;
    }

    public function getDetails(): ?string {
        return $this->details;
    }

    public function setDetails(?string $details): TeacherAbsenceType {
        $this->details = $details;
        return $this;
    }
}