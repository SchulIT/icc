<?php

namespace App\Response\Api\V1;

use App\Entity\ExamInvigilator as ExamSupervisionEntity;

class ExamSupervision {

    /** @var int */
    private $lesson;

    /** @var Teacher */
    private $teacher;

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return ExamSupervision
     */
    public function setLesson(int $lesson): ExamSupervision {
        $this->lesson = $lesson;
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
     * @return ExamSupervision
     */
    public function setTeacher(Teacher $teacher): ExamSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    public static function fromEntity(ExamSupervisionEntity $supervisionEntity): self {
        return (new static())
            ->setTeacher(Teacher::fromEntity($supervisionEntity->getTeacher()))
            ->setLesson($supervisionEntity->getLesson());
    }
}