<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubjectsData {

    /**
     * @var SubjectData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\SubjectData>')]
    private array $subjects = [ ];

    /**
     * @return SubjectData[]
     */
    public function getSubjects() {
        return $this->subjects;
    }

    /**
     * @param SubjectData[] $subjects
     */
    public function setSubjects($subjects): SubjectsData {
        $this->subjects = $subjects;
        return $this;
    }
}