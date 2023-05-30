<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionsData {

    use SuppressNotificationTrait;
    use ContextTrait;

    /**
     * @var SubstitutionData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\SubstitutionData>')]
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