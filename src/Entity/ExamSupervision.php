<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class ExamSupervision {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Exam::class, cascade: ['persist'], inversedBy: 'supervisions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Exam $exam = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Teacher $teacher = null;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private ?int $lesson = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getExam(): Exam {
        return $this->exam;
    }

    public function setExam(Exam $exam): ExamSupervision {
        $this->exam = $exam;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): ExamSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): ExamSupervision {
        $this->lesson = $lesson;
        return $this;
    }
}