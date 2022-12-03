<?php

namespace App\Form\Model;

use App\Entity\DateLesson;
use App\Entity\Student;
use App\Entity\StudentAbsenceType;
use App\Validator\DateLessonGreaterThan;
use App\Validator\DateLessonNotInPast;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class BulkStudentAbsence {
    /**
     * @var Student[]
     */
    #[Assert\Count(min: 1)]
    private array $students = [ ];

    #[DateLessonNotInPast(exceptions: ['ROLE_STUDENT_ABSENCE_CREATOR'], propertyName: 'from')]
    #[Assert\NotNull]
    private ?DateLesson $from = null;

    #[DateLessonGreaterThan(propertyPath: 'from')]
    #[Assert\NotNull]
    private ?DateLesson $until = null;

    #[Assert\NotNull]
    private ?StudentAbsenceType $type = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $phone = null;

    #[Assert\NotBlank]
    private ?string $message = null;

    public function getStudents(): array {
        return $this->students;
    }

    public function setStudents(array $students): BulkStudentAbsence {
        $this->students = $students;
        return $this;
    }

    public function getFrom(): ?DateLesson {
        return $this->from;
    }

    public function setFrom(?DateLesson $from): BulkStudentAbsence {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): ?DateLesson {
        return $this->until;
    }

    public function setUntil(?DateLesson $until): BulkStudentAbsence {
        $this->until = $until;
        return $this;
    }

    public function getType(): ?StudentAbsenceType {
        return $this->type;
    }

    public function setType(?StudentAbsenceType $type): BulkStudentAbsence {
        $this->type = $type;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): BulkStudentAbsence {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(?string $phone): BulkStudentAbsence {
        $this->phone = $phone;
        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): BulkStudentAbsence {
        $this->message = $message;
        return $this;
    }
}