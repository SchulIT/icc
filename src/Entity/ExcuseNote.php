<?php

namespace App\Entity;

use App\Validator\DateIsNotInPast;
use App\Validator\DateLessonGreaterThan;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class ExcuseNote {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Student $student = null;

    #[Assert\NotNull]
    #[ORM\Embedded(class: DateLesson::class)]
    private ?DateLesson $from = null;

    #[DateLessonGreaterThan(propertyPath: 'from')]
    #[Assert\NotNull]
    #[ORM\Embedded(class: DateLesson::class)]
    private ?DateLesson $until = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn]
    private ?Teacher $excusedBy = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): ExcuseNote {
        $this->student = $student;
        return $this;
    }

    public function getFrom(): ?DateLesson {
        return $this->from;
    }

    public function setFrom(?DateLesson $from): ExcuseNote {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): ?DateLesson {
        return $this->until;
    }

    public function setUntil(?DateLesson $until): ExcuseNote {
        $this->until = $until;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): ExcuseNote {
        $this->comment = $comment;
        return $this;
    }

    public function getExcusedBy(): ?Teacher {
        return $this->excusedBy;
    }

    public function setExcusedBy(?Teacher $excusedBy): ExcuseNote {
        $this->excusedBy = $excusedBy;
        return $this;
    }

    /**
     * Check if excuse note applies to a given lesson on a given day.
     */
    public function appliesToLesson(DateTime $dateTime, int $lesson): bool {
        $dateLesson = (new DateLesson())
            ->setDate((clone $dateTime)->setTime(0,0,0))
            ->setLesson($lesson);

        return $dateLesson->isBetween($this->getFrom(), $this->getUntil());
    }
}