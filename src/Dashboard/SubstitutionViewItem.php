<?php

namespace App\Dashboard;

use App\Entity\Substitution;

class SubstitutionViewItem extends AbstractViewItem {

    /** @var bool */
    private $isFreeLessonType;

    private $substitution;

    public function __construct(Substitution $substitution, bool $isFreeLessonType) {
        $this->substitution = $substitution;
        $this->isFreeLessonType = $isFreeLessonType;
    }

    public function isFreeLesson(): bool {
        return $this->isFreeLessonType;
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