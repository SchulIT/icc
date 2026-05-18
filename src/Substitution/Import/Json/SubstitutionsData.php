<?php

namespace App\Substitution\Import\Json;

use App\Framework\Validator\UniqueId;
use App\Framework\Import\Json\ContextTrait;
use App\Framework\Import\Json\SuppressNotificationTrait;
use App\Substitution\Import\Json\SubstitutionData;
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
    #[Serializer\Type('array<' . SubstitutionData::class . '>')]
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