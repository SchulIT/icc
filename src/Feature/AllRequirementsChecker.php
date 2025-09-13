<?php

namespace App\Feature;

use Override;

class AllRequirementsChecker implements RequirementCheckerInterface {

    #[Override]
    public function supports(?Requires $requires): bool {
        return $requires !== null && $requires->requirement === Requirement::All;
    }

    #[Override]
    public function isRequirementFulfilled(?Requires $requires, FeatureManager $featureManager): bool {
        foreach($requires->features as $feature) {
            if($featureManager->isFeatureEnabled($feature) !== true) {
                return false;
            }
        }

        return true;
    }
}