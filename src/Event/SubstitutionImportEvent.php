<?php

namespace App\Event;

use App\Entity\Substitution;
use Symfony\Component\EventDispatcher\Event;

class SubstitutionImportEvent extends Event {
    /** @var Substitution[] */
    private $substitutions;

    /**
     * @param Substitution[] $substitutions
     */
    public function __construct(array $substitutions = []) {
        $this->substitutions = $substitutions;
    }

    /**
     * @return Substitution[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }
}