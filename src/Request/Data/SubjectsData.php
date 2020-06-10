<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubjectsData {

    /**
     * @Serializer\Type("array<App\Request\Data\SubjectData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var SubjectData[]
     */
    private $subjects = [ ];

    /**
     * @return SubjectData[]
     */
    public function getSubjects() {
        return $this->subjects;
    }

    /**
     * @param SubjectData[] $subjects
     * @return SubjectsData
     */
    public function setSubjects($subjects): SubjectsData {
        $this->subjects = $subjects;
        return $this;
    }
}