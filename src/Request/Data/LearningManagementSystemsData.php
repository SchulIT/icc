<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LearningManagementSystemsData {

    #[Serializer\Type('array<App\Request\Data\LearningManagementSystemData>')]
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