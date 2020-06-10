<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionsData {

    /**
     * @Serializer\Type("array<App\Request\Data\SubstitutionData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var SubstitutionData[]
     */
    private $substitutions = [ ];

    /**
     * @return SubstitutionData[]
     */
    public function getSubstitutions() {
        return $this->substitutions;
    }

    /**
     * @param SubstitutionData[] $substitutions
     * @return SubstitutionsData
     */
    public function setSubstitutions($substitutions): SubstitutionsData {
        $this->substitutions = $substitutions;
        return $this;
    }
}