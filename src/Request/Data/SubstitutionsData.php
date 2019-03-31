<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionsData {

    /**
     * @Serializer\Type("array<App\Request\Data\SubstitutionData>")
     * @Assert\Valid()
     * @var SubstitutionData[]
     */
    private $substitutions;

    /**
     * @return SubstitutionData[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }

    /**
     * @param SubstitutionData[] $substitutions
     * @return SubstitutionsData
     */
    public function setSubstitutions(array $substitutions): SubstitutionsData {
        $this->substitutions = $substitutions;
        return $this;
    }
}