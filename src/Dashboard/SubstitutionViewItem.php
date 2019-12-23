<?php

namespace App\Dashboard;

use App\Entity\Substitution;

class SubstitutionViewItem extends AbstractViewItem {

    private $substitution;

    public function __construct(Substitution $substitution) {
        $this->substitution = $substitution;
    }

    /**
     * @return Substitution
     */
    public function getSubstitution(): Substitution {
        return $this->substitution;
    }

    public function getBlockName(): string {
        return 'substitution';
    }
}