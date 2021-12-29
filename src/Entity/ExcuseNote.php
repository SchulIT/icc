<?php

namespace App\Entity;

use App\Validator\DateLessonGreaterThan;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class ExcuseNote {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Student|null
     */
    private $student;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @Assert\NotNull()
     * @var DateLesson|null
     */
    private $from;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @DateLessonGreaterThan(propertyPath="from")
     * @Assert\NotNull()
     * @var DateLesson|null
     */
    private $until;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn()
     * @Assert\NotNull()
     * @var Teacher|null
     */
    private $excusedBy;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return ExcuseNote
     */
    public function setStudent(?Student $student): ExcuseNote {
        $this->student = $student;
        return $this;
    }

    /**
     * @return DateLesson|null
     */
    public function getFrom(): ?DateLesson {
        return $this->from;
    }

    /**
     * @param DateLesson|null $from
     * @return ExcuseNote
     */
    public function setFrom(?DateLesson $from): ExcuseNote {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateLesson|null
     */
    public function getUntil(): ?DateLesson {
        return $this->until;
    }

    /**
     * @param DateLesson|null $until
     * @return ExcuseNote
     */
    public function setUntil(?DateLesson $until): ExcuseNote {
        $this->until = $until;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return ExcuseNote
     */
    public function setComment(?string $comment): ExcuseNote {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getExcusedBy(): ?Teacher {
        return $this->excusedBy;
    }

    /**
     * @param Teacher|null $excusedBy
     * @return ExcuseNote
     */
    public function setExcusedBy(?Teacher $excusedBy): ExcuseNote {
        $this->excusedBy = $excusedBy;
        return $this;
    }

    /**
     * Check if excuse note applies to a given lesson on a given day.
     *
     * @param DateTime $dateTime
     * @param int $lesson
     * @return bool
     */
    public function appliesToLesson(DateTime $dateTime, int $lesson): bool {
        if($dateTime < $this->getFrom()->getDate() || $dateTime > $this->getUntil()->getDate()) {
            return false;
        }

        if($this->getFrom()->getDate() < $dateTime && $dateTime < $this->getUntil()->getDate()) {
            return true;
        }

        if($this->getFrom()->getDate() == $dateTime && $this->getFrom()->getLesson() <= $lesson) {
            return true;
        }

        if($this->getUntil()->getDate() == $dateTime && $this->getUntil()->getLesson() >= $lesson) {
            return true;
        }

        return false;
    }
}