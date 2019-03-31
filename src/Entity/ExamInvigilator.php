<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ExamInvigilator {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Exam", inversedBy="invigilators", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Exam
     */
    private $exam;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher
     */
    private $teacher;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lesson;

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return Exam
     */
    public function getExam(): Exam {
        return $this->exam;
    }

    /**
     * @param Exam $exam
     * @return ExamInvigilator
     */
    public function setExam(Exam $exam): ExamInvigilator {
        $this->exam = $exam;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return ExamInvigilator
     */
    public function setTeacher(Teacher $teacher): ExamInvigilator {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return ExamInvigilator
     */
    public function setLesson(int $lesson): ExamInvigilator {
        $this->lesson = $lesson;
        return $this;
    }
}