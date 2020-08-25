<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamsData {

    /**
     * @Serializer\Type("array<App\Request\Data\ExamData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var ExamData[]
     */
    private $exams = [ ];

    /**
     * @return ExamData[]
     */
    public function getExams() {
        return $this->exams;
    }

    /**
     * @param ExamData[] $exams
     * @return ExamsData
     */
    public function setExams($exams): ExamsData {
        $this->exams = $exams;
        return $this;
    }
}