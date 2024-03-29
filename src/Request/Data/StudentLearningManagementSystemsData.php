<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentLearningManagementSystemsData {

    /**
     * @var array<StudentLearningManagementSystemData>
     */
    #[Serializer\Type('array<App\Request\Data\StudentLearningManagementSystemData>')]
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