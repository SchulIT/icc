<?php

namespace App\Response\Api\V1;

use App\Entity\ExamSupervision as ExamSupervisionEntity;
use JMS\Serializer\Annotation as Serializer;

class ExamSupervision {

    /**
     * @Serializer\SerializedName("lesson")
     * @Serializer\Type("int")
     */
    private ?int $lesson = null;

    /**
     * @Serializer\SerializedName("teacher")
     * @Serializer\Type("App\Response\Api\V1\Teacher")
     */
    private ?Teacher $teacher = null;

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): ExamSupervision {
        $this->lesson = $lesson;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): ExamSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    public static function fromEntity(ExamSupervisionEntity $supervisionEntity): self {
        return (new self())
            ->setTeacher(Teacher::fromEntity($supervisionEntity->getTeacher()))
            ->setLesson($supervisionEntity->getLesson());
    }
}