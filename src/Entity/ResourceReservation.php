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
     * @Assert\NotNull()
     * @var ResourceEntity|null
     */
    private $resource;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @DateIsNotInPast()
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart = 0;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Teacher|null
     */
    private $teacher;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return ResourceEntity|null
     */
    public function getResource(): ?ResourceEntity {
        return $this->resource;
    }

    /**
     * @param ResourceEntity|null $resource
     * @return ResourceReservation
     */
    public function setResource(?ResourceEntity $resource): ResourceReservation {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return ResourceReservation
     */
    public function setDate(?DateTime $date): ResourceReservation {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return ResourceReservation
     */
    public function setLessonStart(int $lessonStart): ResourceReservation {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return ResourceReservation
     */
    public function setLessonEnd(int $lessonEnd): ResourceReservation {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return ResourceReservation
     */
    public function setTeacher(?Teacher $teacher): ResourceReservation {
        $this->teacher = $teacher;
        return $this;
    }

}