<?php

namespace App\LearningManagementSystem\View\Filter;

use App\LearningManagementSystem\Entity\LearningManagementSystem;

class LearningManagementSystemFilterView {
    /**
     * @param LearningManagementSystem[] $learningManagementSystems
     * @param LearningManagementSystem|null $currentLearningManagementSystem
     */
    public function __construct(private readonly array $learningManagementSystems, private readonly ?LearningManagementSystem $currentLearningManagementSystem) { }

    /**
     * @return LearningManagementSystem|null
     */
    public function getCurrentLearningManagementSystem(): ?LearningManagementSystem {
        return $this->currentLearningManagementSystem;
    }

    /**
     * @return LearningManagementSystem[]
     */
    public function getLearningManagementSystems(): array {
        return $this->learningManagementSystems;
    }
}