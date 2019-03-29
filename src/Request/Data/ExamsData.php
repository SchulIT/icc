<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamsData {

    /**
     * @Serializer\Type("array<ExamData>")
     * @Assert\Valid()
     * @var ExamData[]
     */
    private $exams;

    /**
     * @return ExamData[]
     */
    public function getExams(): array {
        return $this->exams;
    }

    /**
     * @param ExamData[] $exams
     * @return ExamsData
     */
    public function setExams(array $exams): ExamsData {
        $this->exams = $exams;
        return $this;
    }
}