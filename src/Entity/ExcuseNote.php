<?php

namespace App\Entity;

use DateTime;
use DH\DoctrineAuditBundle\Annotation\Auditable;
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
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(1)
     * @var int
     */
    private $lessonStart = 1;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd = 1;

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
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return ExcuseNote
     */
    public function setDate(?DateTime $date): ExcuseNote {
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
     * @return ExcuseNote
     */
    public function setLessonStart(int $lessonStart): ExcuseNote {
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
     * @return ExcuseNote
     */
    public function setLessonEnd(int $lessonEnd): ExcuseNote {
        $this->lessonEnd = $lessonEnd;
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
}