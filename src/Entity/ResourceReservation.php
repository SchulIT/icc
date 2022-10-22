<?php

namespace App\Entity;

use App\Validator\DateIsNotInPast;
use App\Validator\NoReservationCollision;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @NoReservationCollision(groups={"Default", "collision"})
 */
class ResourceReservation {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="ResourceEntity")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?ResourceEntity $resource = null;

    /**
     * @ORM\Column(type="date")
     * @DateIsNotInPast()
     */
    #[Assert\NotNull]
    private ?\DateTime $date = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThan(0)]
    private int $lessonStart = 0;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private int $lessonEnd = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?Teacher $teacher = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getResource(): ?ResourceEntity {
        return $this->resource;
    }

    public function setResource(?ResourceEntity $resource): ResourceReservation {
        $this->resource = $resource;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): ResourceReservation {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): ResourceReservation {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): ResourceReservation {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): ResourceReservation {
        $this->teacher = $teacher;
        return $this;
    }

}