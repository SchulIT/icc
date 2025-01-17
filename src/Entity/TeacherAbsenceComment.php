<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TeacherAbsenceComment {
    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: TeacherAbsence::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TeacherAbsence $absence;

    #[ORM\Column(type: 'date')]
    private DateTime $date;

    #[ORM\Column(type: 'integer')]
    private int $lessonStart;

    #[ORM\Column(type: 'integer')]
    private int $lessonEnd;

    #[ORM\ManyToOne(targetEntity: Tuition::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Tuition $tuition;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $comment;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return TeacherAbsence
     */
    public function getAbsence(): TeacherAbsence {
        return $this->absence;
    }

    /**
     * @param TeacherAbsence $absence
     * @return TeacherAbsenceComment
     */
    public function setAbsence(TeacherAbsence $absence): TeacherAbsenceComment {
        $this->absence = $absence;
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
     * @return TeacherAbsenceComment
     */
    public function setComment(?string $comment): TeacherAbsenceComment {
        $this->comment = $comment;
        return $this;
    }

    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    public function setTuition(?Tuition $tuition): TeacherAbsenceComment {
        $this->tuition = $tuition;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): TeacherAbsenceComment {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): TeacherAbsenceComment {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): TeacherAbsenceComment {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }
}