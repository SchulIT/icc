<?php

namespace App\Feature;

use Override;

class EmptyRequirementChecker implements RequirementCheckerInterface {

    #[Override]
    public function supports(?Requires $requires): bool {
        return $requires === null;
    }

    #[Override]
    public function isRequirementFulfilled(?Requires $requires, FeatureManager $featureManager): bool {
        return true;
    }
}