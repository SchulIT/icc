<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class ExamList {

    /**
     * @Serializer\SerializedName("exams")
     * @Serializer\Type("array<App\Response\Api\V1\Exam>")
     * @var Exam[]
     */
    private ?array $exams = null;

    /**
     * @return Exam[]
     */
    public function getExams(): array {
        return $this->exams;
    }

    /**
     * @param Exam[] $exams
     */
    public function setExams(array $exams): ExamList {
        $this->exams = $exams;
        return $this;
    }
}