<?php

namespace App\LearningManagementSystem\Import\Json;

use App\LearningManagementSystem\Import\Json\StudentLearningManagementSystemData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentLearningManagementSystemsData {

    /**
     * @var array<StudentLearningManagementSystemData>
     */
    #[Serializer\Type('array<' . StudentLearningManagementSystemData::class . '>')]
    #[Assert\Valid]
    private array $consents = [ ];

    public function getConsents(): array {
        return $this->consents;
    }

    public function setConsents(array $consents): StudentLearningManagementSystemsData {
        $this->consents = $consents;
        return $this;
    }
}