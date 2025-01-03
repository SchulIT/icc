<?php

namespace App\Entity;

use App\Validator\DateIsNotInPast;
use App\Validator\NoReservationCollision;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[NoReservationCollision(groups: ['Default', 'collision'])]
#[ORM\Entity]
class ResourceReservation {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: ResourceEntity::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?ResourceEntity $resource = null;

    #[DateIsNotInPast]
    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $lessonStart = 0;

    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    #[ORM\Column(type: 'integer')]
    private int $lessonEnd = 0;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(targetEntity: StudyGroup::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?StudyGroup $associatedStudyGroup = null;

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

    public function getAssociatedStudyGroup(): ?StudyGroup {
        return $this->associatedStudyGroup;
    }

    public function setAssociatedStudyGroup(?StudyGroup $associatedStudyGroup): void {
        $this->associatedStudyGroup = $associatedStudyGroup;
    }

}