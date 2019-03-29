<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubjectsData {

    /**
     * @Serializer\Type("array<SubjectData>")
     * @Assert\Valid()
     * @var SubjectData[]
     */
    private $subjects;

    /**
     * @return SubjectData[]
     */
    public function getSubjects(): array {
        return $this->subjects;
    }

    /**
     * @param SubjectData[] $subjects
     * @return SubjectsData
     */
    public function setSubjects(array $subjects): SubjectsData {
        $this->subjects = $subjects;
        return $this;
    }
}