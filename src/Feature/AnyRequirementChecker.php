<?php

namespace App\Feature;

use Override;

class AnyRequirementChecker implements RequirementCheckerInterface {
    #[Override]
    public function supports(?Requires $requires): bool {
        return $requires !== null && $requires->requirement === Requirement::Any;
    }

    #[Override]
    public function isRequirementFulfilled(?Requires $requires, FeatureManager $featureManager): bool {
        foreach($requires->features as $feature) {
            if($featureManager->isFeatureEnabled($feature)) {
                return true;
            }
        }

        return false;
    }
}