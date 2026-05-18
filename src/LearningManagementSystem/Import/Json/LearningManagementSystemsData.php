<?php

namespace App\LearningManagementSystem\Import\Json;

use App\LearningManagementSystem\Import\Json\LearningManagementSystemData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LearningManagementSystemsData {

    #[Serializer\Type('array<' . LearningManagementSystemData::class . '>')]
    #[Assert\Valid]
    private array $lms = [ ];

    /**
     * @return LearningManagementSystemData[]
     */
    public function getLms(): array {
        return $this->lms;
    }

    public function setLms(array $lms): LearningManagementSystemsData {
        $this->lms = $lms;
        return $this;
    }
}