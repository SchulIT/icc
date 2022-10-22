<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionsData {

    use SuppressNotificationTrait;
    use ContextTrait;

    /**
     * @Serializer\Type("array<App\Request\Data\SubstitutionData>")
     * @UniqueId(propertyPath="id")
     * @var SubstitutionData[]
     */
    #[Assert\Valid]
    private array $substitutions = [ ];

    /**
     * @return SubstitutionData[]
     */
    public function getSubstitutions() {
        return $this->substitutions;
    }

    /**
     * @param SubstitutionData[] $substitutions
     */
    public function setSubstitutions($substitutions): SubstitutionsData {
        $this->substitutions = $substitutions;
        return $this;
    }

}