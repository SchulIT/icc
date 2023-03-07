<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TeacherAbsenceLesson {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: TeacherAbsence::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TeacherAbsence $absence;

    #[ORM\ManyToOne(targetEntity: TimetableLesson::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotNull]
    private ?TimetableLesson $lesson;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $commentTeacher;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $commentStudents;

    #[ORM\Column(type: 'string', nullable: true)]
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
     * @return TeacherAbsenceLesson
     */
    public function setAbsence(TeacherAbsence $absence): TeacherAbsenceLesson {
        $this->absence = $absence;
        return $this;
    }

    /**
     * @return TimetableLesson|null
     */
    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    /**
     * @param TimetableLesson|null $lesson
     * @return TeacherAbsenceLesson
     */
    public function setLesson(?TimetableLesson $lesson): TeacherAbsenceLesson {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentTeacher(): ?string {
        return $this->commentTeacher;
    }

    /**
     * @param string|null $commentTeacher
     * @return TeacherAbsenceLesson
     */
    public function setCommentTeacher(?string $commentTeacher): TeacherAbsenceLesson {
        $this->commentTeacher = $commentTeacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentStudents(): ?string {
        return $this->commentStudents;
    }

    /**
     * @param string|null $commentStudents
     * @return TeacherAbsenceLesson
     */
    public function setCommentStudents(?string $commentStudents): TeacherAbsenceLesson {
        $this->commentStudents = $commentStudents;
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
     * @return TeacherAbsenceLesson
     */
    public function setComment(?string $comment): TeacherAbsenceLesson {
        $this->comment = $comment;
        return $this;
    }
}