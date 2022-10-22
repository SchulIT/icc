<?php

namespace App\Display;

use App\Entity\Substitution;

class SubstitutionViewItem extends AbstractViewItem {

    private Substitution $substitution;

    public function __construct(Substitution $substitution) {
        parent::__construct($substitution->getLessonStart(), $substitution->startsBefore());
        $this->substitution = $substitution;
    }

    public function getSubstitution(): Substitution {
        return $this->substitution;
    }

    public function getName(): string {
        return 'substitution';
    }

    /**
     * @inheritDoc
     */
    public function getSortingIndex(): int {
        return 3;
    }
}