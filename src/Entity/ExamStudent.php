<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

#[Auditable]
#[ORM\Entity]
#[ORM\Table('exam_students')]
class ExamStudent {
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Exam::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Exam $exam;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Tuition::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Tuition $tuition;

    public function getExam(): Exam {
        return $this->exam;
    }

    public function setExam(Exam $exam): ExamStudent {
        $this->exam = $exam;
        return $this;
    }
    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): ExamStudent {
        $this->student = $student;
        return $this;
    }

    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    public function setTuition(?Tuition $tuition): ExamStudent {
        $this->tuition = $tuition;
        return $this;
    }
}